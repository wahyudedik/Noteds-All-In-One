# 📝 Noteds — All-in-One Notes Platform

Platform catatan all-in-one yang dibangun dengan **Laravel** dan **Svelte 5** menggunakan arsitektur **Inertia.js**. Dirancang untuk mengelola berbagai jenis catatan — dari pekerjaan, pribadi, keuangan, keluarga, hingga proyek kreatif — dalam satu tempat.

---

## 🚀 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | [Laravel](https://laravel.com) (PHP 8.3+) |
| **Frontend** | [Svelte 5](https://svelte.dev) + [Inertia.js](https://inertiajs.com) |
| **Styling** | [Tailwind CSS v4](https://tailwindcss.com) |
| **Build Tool** | [Vite 8](https://vite.dev) |
| **Database** | SQLite (default), MySQL/MariaDB |
| **Autentikasi** | [Laravel Fortify](https://fortify.laravel.com) |
| **Keamanan** | 2FA (TOTP), Passkey/WebAuthn |
| **Tim** | Manajemen tim, undangan, role-based access |
| **Code Quality** | PHPStan/Larastan, Laravel Pint, ESLint, Prettier |
| **Testing** | PHPUnit |
| **CI/CD** | GitHub Actions |
| **Deploy** | VPS (Bash script) |

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan
- Registrasi & Login (email + password)
- Verifikasi email
- Reset password
- **Two-Factor Authentication (2FA)** — via TOTP (Google Authenticator, dll.)
- **Passkey / WebAuthn** — login tanpa password menggunakan biometrik/hardware key
- Enkripsi session & sensitive data

### 👥 Manajemen Tim
- Buat & kelola multiple tim
- **Undangan anggota** — via email, accept/decline
- **Role-based access** — Owner, Admin, Member
- **Switching tim** — beralih antar tim dengan mudah
- Personal team (otomatis dibuat saat registrasi)
- Kelola anggota (tambah, edit role, hapus)

### 📊 Dashboard
- Ringkasan aktivitas terkini
- Overview catatan & tugas

### ⚙️ Pengaturan Profil
- Edit nama & email
- Ubah password
- Kelola keamanan (2FA & Passkey)
- Hapus akun

### 🎨 Tampilan
- **Dark/Light mode** — toggle tema sesuai preferensi
- UI responsif dengan Tailwind CSS

---

## 📂 Struktur Projek

```
noteds-all-in-one/
├── app/
│   ├── Actions/           # Fortify actions (register, reset password, teams)
│   ├── Concerns/          # Traits (HasTeams, password validation, dll.)
│   ├── Console/           # Artisan commands
│   ├── Data/              # Data transfer objects
│   ├── Enums/             # Enums (TeamRole, TeamPermission)
│   ├── Http/
│   │   ├── Controllers/   # Controllers (Dashboard, Settings, Teams)
│   │   ├── Middleware/     # Middleware (auth, team membership, dll.)
│   │   ├── Requests/      # Form request validation
│   │   └── Responses/     # Custom response handlers
│   ├── Models/            # Eloquent models (User, Team, Membership, dll.)
│   ├── Notifications/     # Email notifications (team invitation)
│   ├── Policies/          # Authorization policies
│   ├── Providers/         # Service providers
│   └── Rules/             # Custom validation rules
├── config/                # Konfigurasi Laravel & Fortify
├── database/
│   ├── factories/         # Model factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── public/                # Public assets (favicon, fonts, dll.)
├── resources/
│   ├── css/               # Tailwind CSS entry point
│   └── js/
│       ├── actions/       # Inertia action handlers
│       ├── components/    # Svelte UI components
│       ├── layouts/       # Layout components (App, Auth, Settings)
│       ├── lib/           # Utility libraries
│       ├── pages/         # Page components (Dashboard, Auth, Settings, Teams)
│       ├── routes/        # Wayfinder route definitions
│       └── types/         # TypeScript type definitions
├── routes/                # Route definitions (web.php, settings.php)
├── storage/               # Cache, sessions, views
├── tests/                 # PHPUnit tests
├── deploy.sh              # VPS deployment script
└── .github/workflows/     # CI/CD pipelines
```

---

## 🏗️ Database Schema

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (name, email, password, 2FA columns) |
| `teams` | Tim (name, slug, is_personal) |
| `team_members` | Relasi anggota tim dengan role |
| `team_invitations` | Undangan tim yang pending |
| `cache` | Cache storage |
| `jobs` | Queue jobs |
| `sessions` | Session storage |
| `passkeys` | WebAuthn/Passkey credentials |

---

## 📦 Instalasi

### Prasyarat
- PHP 8.3+
- Node.js 22+
- Composer 2
- SQLite atau MySQL

### Langkah Setup

```bash
# 1. Clone repository
git clone https://github.com/your-username/noteds-all-in-one.git
cd noteds-all-in-one

# 2. Install dependencies PHP
composer install

# 3. Install dependencies JS
npm install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Setup database
php artisan migrate

# 6. Jalankan development server
npm run dev
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`.

---

## 🔧 Perintah Development

| Perintah | Deskripsi |
|----------|-----------|
| `npm run dev` | Jalankan Vite dev server (hot reload) |
| `npm run build` | Build frontend untuk production |
| `php artisan serve` | Jalankan Laravel dev server |
| `composer lint` | Format PHP dengan Laravel Pint |
| `npm run lint` | Lint & fix JavaScript/Svelte |
| `npm run format` | Format code dengan Prettier |
| `npm run types:check` | Type checking TypeScript |
| `php artisan test` | Jalankan PHPUnit tests |

---

## 🚢 Deployment

### VPS Deployment

```bash
# Setup pertama kali (install git hooks)
bash deploy.sh --setup

# Deploy update
./deploy.sh
```

Script deployment otomatis:
1. Pull latest code dari branch `main`
2. Install/update Composer dependencies
3. Install/update NPM dependencies
4. Build frontend assets
5. Jalankan database migrations
6. Restart queue worker

### Environment Variables Penting

```env
APP_NAME="Noteds All In One"
APP_ENV=production
APP_URL=https://your-domain.com
DB_CONNECTION=sqlite
SESSION_DRIVER=database
```

---

## 🔒 Fitur Keamanan

| Fitur | Status |
|-------|--------|
| CSRF Protection | ✅ |
| XSS Protection | ✅ |
| SQL Injection Prevention | ✅ (Eloquent ORM) |
| Rate Limiting | ✅ |
| Two-Factor Authentication | ✅ |
| Passkey / WebAuthn | ✅ |
| Email Verification | ✅ |
| Session Encryption | ✅ |
| Password Hashing (bcrypt) | ✅ |

---

## 🧪 CI/CD

### GitHub Actions Workflows

| Workflow | Trigger | Deskripsi |
|----------|---------|-----------|
| **tests** | Push/PR to main/develop | Jalankan PHPUnit tests (PHP 8.3, 8.4, 8.5) |
| **linter** | Push/PR to main/develop | PHP Pint, ESLint, Prettier check |

---

## 📋 Roadmap

### ✅ Phase 1 — Fondasi (Selesai)
- [x] Autentikasi (Register, Login, Logout)
- [x] Verifikasi Email
- [x] Reset Password
- [x] Two-Factor Authentication (2FA)
- [x] Passkey / WebAuthn
- [x] Manajemen Profil
- [x] Manajemen Tim (Create, Invitations, Members, Roles)
- [x] Dashboard
- [x] Dark/Light Mode
- [x] CI/CD Pipeline

### ✅ Phase 2 — Core Notes (Selesai)
- [x] CRUD Catatan (multi-tipe)
- [x] Kategori & Label
- [x] Pencarian Cerdas (full-text search)
- [x] Rich Text Editor

### 🔜 Phase 3 — Produktivitas
- [ ] Tugas & Deadline (Kanban board)
- [ ] Kalender Terintegrasi
- [ ] Pengingat & Notifikasi

### 🔜 Phase 4 — Kolaborasi
- [ ] Berbagi Catatan (share link)
- [ ] Komentar & Diskusi
- [ ] Real-time Collaboration

### 🔜 Phase 5 — Lanjutan
- [ ] Versi & Riwayat Catatan
- [ ] Enkripsi End-to-End
- [ ] Export/Import (PDF, Markdown, JSON)
- [ ] API untuk integrasi pihak ketiga
- [ ] Mobile app (React Native / Flutter)

---

## 📄 Lisensi

Proyek ini merupakan proyek pribadi. Hak cipta dilindungi.

---

## 👤 Author

Dibangun dengan ❤️ untuk produktivitas yang lebih baik.
#   N o t e d s - A l l - I n - O n e 
 
 
