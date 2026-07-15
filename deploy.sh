#!/bin/bash

# ============================================================
# Qalcuity - VPS Deployment Script
# Jalankan langsung di VPS untuk pull & update aplikasi
#
# Setup pertama kali:
#   bash deploy.sh --setup
#
# Update:
#   ./deploy.sh
# ============================================================

set -e

# ---- Konfigurasi ----
APP_PATH="/www/wwwroot/qalcuity.com"
BRANCH="main"
LOG_FILE="${APP_PATH}/storage/logs/deploy.log"

# ---- Setup: Install git hook post-merge untuk auto-chmod ----
if [ "$1" = "--setup" ]; then
    echo "Installing git hooks..."
    mkdir -p .git/hooks

    cat > .git/hooks/post-merge << 'HOOK'
#!/bin/bash
if [ -f deploy.sh ]; then
    chmod +x deploy.sh
fi
HOOK
    chmod +x .git/hooks/post-merge
    chmod +x deploy.sh

    echo "✓ Git hook 'post-merge' berhasil diinstall."
    echo "✓ deploy.sh sekarang executable."
    echo ""
    echo "Sekarang jalankan: ./deploy.sh"
    exit 0
fi

# ---- Warna ----
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

log_info()    { echo -e "${BLUE}[INFO]${NC} $1"; echo "[$(date '+%Y-%m-%d %H:%M:%S')] [INFO] $1" >> "${LOG_FILE}" 2>/dev/null || true; }
log_success() { echo -e "${GREEN}[OK]${NC} $1"; echo "[$(date '+%Y-%m-%d %H:%M:%S')] [OK] $1" >> "${LOG_FILE}" 2>/dev/null || true; }
log_error()   { echo -e "${RED}[ERROR]${NC} $1"; echo "[$(date '+%Y-%m-%d %H:%M:%S')] [ERROR] $1" >> "${LOG_FILE}" 2>/dev/null || true; exit 1; }

# ---- Header ----
echo ""
echo -e "${CYAN}===========================================================${NC}"
echo -e "${CYAN}  Qalcuity - Deploy $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${CYAN}===========================================================${NC}"
echo ""

# ---- Fix ownership (prevent permission denied) ----
# Fix entire project directory so the current user can write during git pull.
# Files may be owned by www (web server) or root from previous deploys.
if [ "$(id -u)" -eq 0 ]; then
    chown -R "$(stat -c '%u:%g' "${APP_PATH}" 2>/dev/null || id -u):$(id -g)" "${APP_PATH}" 2>/dev/null || true
fi

# ---- Cek direktori ----
cd "${APP_PATH}" || log_error "Direktori tidak ditemukan: ${APP_PATH}"
[ -d ".git" ] || log_error "Bukan git repository!"

# ---- Fetch & cek update ----
log_info "Fetching perubahan dari remote..."
git fetch origin

LOCAL_HASH=$(git rev-parse HEAD)
REMOTE_HASH=$(git rev-parse "origin/${BRANCH}")

if [ "${LOCAL_HASH}" = "${REMOTE_HASH}" ]; then
    log_success "Sudah versi terbaru ($(git log --oneline -1)). Tidak ada yang perlu diupdate."
    exit 0
fi

log_info "Update: $(git log --oneline -1 HEAD) → $(git log --oneline -1 origin/${BRANCH})"

# ---- Maintenance mode ----
log_info "Maintenance mode ON..."
php artisan down --render="errors::503" --retry=60 || true

# ---- Update kode (fetch + reset, avoids merge conflicts) ----
log_info "Reset ke origin/${BRANCH}..."
git reset --hard "origin/${BRANCH}" || {
    log_error "Reset gagal."
    php artisan up
    exit 1
}
git clean -fd 2>/dev/null || true

log_success "Kode di-update: $(git log --oneline -1)"

# ---- Composer install ----
log_info "Composer install..."
# Menghindari error plugin terblokir saat dijalankan sebagai root
if [ "$(id -u)" -eq 0 ]; then
    export COMPOSER_ALLOW_SUPERUSER=1
fi
composer install --no-dev --optimize-autoloader --no-interaction

# ---- Pastikan .env ada ----
if [ ! -f .env ]; then
    log_info "Copy .env dari .env.production..."
    cp .env.production .env
    php artisan key:generate --force --no-interaction
fi

# ---- Backup database sebelum migration ----
log_info "Backup database sebelum migration..."
BACKUP_DIR="${APP_PATH}/storage/backups"
mkdir -p "${BACKUP_DIR}"
BACKUP_FILE="${BACKUP_DIR}/db_$(date +%Y%m%d_%H%M%S).sql"

DB_HOST=$(grep -E "^DB_HOST=" .env 2>/dev/null | cut -d '=' -f 2 | tr -d '"' | tr -d "'")
DB_PORT=$(grep -E "^DB_PORT=" .env 2>/dev/null | cut -d '=' -f 2 | tr -d '"' | tr -d "'")
DB_DATABASE=$(grep -E "^DB_DATABASE=" .env 2>/dev/null | cut -d '=' -f 2 | tr -d '"' | tr -d "'")
DB_USERNAME=$(grep -E "^DB_USERNAME=" .env 2>/dev/null | cut -d '=' -f 2 | tr -d '"' | tr -d "'")
DB_PASSWORD=$(grep -E "^DB_PASSWORD=" .env 2>/dev/null | cut -d '=' -f 2 | tr -d '"' | tr -d "'")

if command -v mysqldump &> /dev/null; then
    MYSQL_PWD="${DB_PASSWORD}" mysqldump -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" "${DB_DATABASE}" > "${BACKUP_FILE}" 2>/dev/null && \
        log_success "Database backup: ${BACKUP_FILE}" || \
        log_info "Backup skipped (mysqldump failed — database mungkin belum ada)"
else
    log_info "mysqldump tidak tersedia, skip backup"
fi

# Cleanup backup lama (> 7 hari)
find "${BACKUP_DIR}" -name "db_*.sql" -mtime +7 -delete 2>/dev/null || true

# ---- Migrasi ----
log_info "Migrasi database..."
php artisan migrate --force

# ---- Ensure storage directories exist & are writable ----
log_info "Prepare storage directories..."
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/logs

# ---- Cache (runs as deploy user who owns files from pre-deploy chown) ----
log_info "Rebuild cache..."
php artisan config:cache
php artisan route:cache
php artisan view:clear 2>/dev/null || true
php artisan view:cache
php artisan event:cache

# ---- Build assets ----
log_info "Build frontend assets..."
npm ci --ignore-scripts --cache /tmp/npm-cache || {
    log_error "npm install gagal. Rolling back ke ${PREVIOUS_COMMIT:0:7}..."
    git reset --hard "${PREVIOUS_COMMIT}"
    composer install --no-dev --optimize-autoloader --no-interaction
    php artisan up
    exit 1
}
npm run build

# ---- Storage link ----
[ -L public/storage ] || php artisan storage:link --force

# ---- Restart Services (BEFORE chown — SSH user still has write access) ----
log_info "Restarting queue workers..."
php artisan queue:restart || true

# Reload Octane if running
if php artisan octane:status &>/dev/null; then
    log_info "Reloading Laravel Octane..."
    php artisan octane:reload || true
fi

# Restart Reverb
if command -v supervisorctl &> /dev/null; then
    log_info "Restarting Reverb WebSocket (via Supervisor)..."
    supervisorctl restart qalcuity-reverb || sudo supervisorctl restart qalcuity-reverb || true
else
    # Fallback: kill process on Reverb port so it restarts (if managed by other process managers)
    PORT=$(grep -E "^REVERB_SERVER_PORT=" .env 2>/dev/null | cut -d '=' -f 2 | tr -d '"' | tr -d "'")
    PORT=${PORT:-8080}
    log_info "Supervisor not found. Restarting Reverb on port ${PORT}..."
    fuser -k "${PORT}/tcp" 2>/dev/null || sudo fuser -k "${PORT}/tcp" 2>/dev/null || true
fi

# ---- Maintenance mode OFF (must run BEFORE chown — needs write to storage/framework/) ----
log_info "Maintenance mode OFF..."
php artisan up

# ---- Permissions (restore www ownership — MUST be the LAST step) ----
log_info "Set permissions (www ownership for web server)..."
sudo chown -R www:www "${APP_PATH}" 2>/dev/null || \
    sudo chgrp -R www "${APP_PATH}" 2>/dev/null || true
sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo -e "${GREEN}===========================================================${NC}"
echo -e "${GREEN}  DEPLOY SELESAI! $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${GREEN}===========================================================${NC}"
