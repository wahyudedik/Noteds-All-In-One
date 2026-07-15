<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ── Categories ──────────────────────────────────────
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->default('#6366F1');
            $table->string('icon', 50)->nullable();
            $table->timestamps();

            $table->unique(['team_id', 'name']);
        });

        // ── Labels ──────────────────────────────────────────
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->default('#8B5CF6');
            $table->timestamps();

            $table->unique(['team_id', 'name']);
        });

        // ── Notes ───────────────────────────────────────────
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->json('content');
            $table->text('excerpt')->nullable();
            $table->string('type')->default('text');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'is_pinned', 'updated_at']);
            $table->index(['team_id', 'category_id']);
            $table->index('user_id');
        });

        // ── Label-Note Pivot ────────────────────────────────
        Schema::create('label_note', function (Blueprint $table) {
            $table->foreignId('label_id')->constrained()->cascadeOnDelete();
            $table->foreignId('note_id')->constrained()->cascadeOnDelete();

            $table->primary(['label_id', 'note_id']);
        });

        // ── FTS5 Virtual Table ──────────────────────────────
        // Only create FTS5 table for SQLite
        if ($this->isSQLite()) {
            DB::statement('
                CREATE VIRTUAL TABLE IF NOT EXISTS notes_fts USING fts5(
                    title,
                    content,
                    content=\'notes\',
                    content_rowid=\'id\'
                )
            ');

            // Triggers to keep FTS in sync
            DB::statement('
                CREATE TRIGGER IF NOT EXISTS notes_ai AFTER INSERT ON notes BEGIN
                    INSERT INTO notes_fts(rowid, title, content)
                    VALUES (new.id, new.title, new.excerpt);
                END
            ');

            DB::statement('
                CREATE TRIGGER IF NOT EXISTS notes_ad AFTER DELETE ON notes BEGIN
                    INSERT INTO notes_fts(notes_fts, rowid, title, content)
                    VALUES (\'delete\', old.id, old.title, old.excerpt);
                END
            ');

            DB::statement('
                CREATE TRIGGER IF NOT EXISTS notes_au AFTER UPDATE ON notes BEGIN
                    INSERT INTO notes_fts(notes_fts, rowid, title, content)
                    VALUES (\'delete\', old.id, old.title, old.excerpt);
                    INSERT INTO notes_fts(rowid, title, content)
                    VALUES (new.id, new.title, new.excerpt);
                END
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->isSQLite()) {
            DB::statement('DROP TRIGGER IF EXISTS notes_ai');
            DB::statement('DROP TRIGGER IF EXISTS notes_ad');
            DB::statement('DROP TRIGGER IF EXISTS notes_au');
            DB::statement('DROP TABLE IF EXISTS notes_fts');
        }

        Schema::dropIfExists('label_note');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('categories');
    }

    /**
     * Check if the database is SQLite.
     */
    private function isSQLite(): bool
    {
        return config('database.default') === 'sqlite';
    }
};
