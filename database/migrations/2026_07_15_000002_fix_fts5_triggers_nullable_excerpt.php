<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix FTS5 triggers to handle NULL excerpt values.
     * The original triggers used new.excerpt directly, which could be NULL
     * when a note is first created (excerpt is generated after creation).
     * This caused the FTS5 INSERT to fail, rolling back the entire transaction.
     */
    public function up(): void
    {
        if ($this->isSQLite()) {
            // Drop existing triggers
            DB::statement('DROP TRIGGER IF EXISTS notes_ai');
            DB::statement('DROP TRIGGER IF EXISTS notes_ad');
            DB::statement('DROP TRIGGER IF EXISTS notes_au');

            // Recreate triggers with COALESCE to handle NULL excerpt
            DB::statement('
                CREATE TRIGGER IF NOT EXISTS notes_ai AFTER INSERT ON notes BEGIN
                    INSERT INTO notes_fts(rowid, title, content)
                    VALUES (new.id, new.title, COALESCE(new.excerpt, \'\'));
                END
            ');

            DB::statement('
                CREATE TRIGGER IF NOT EXISTS notes_ad AFTER DELETE ON notes BEGIN
                    INSERT INTO notes_fts(notes_fts, rowid, title, content)
                    VALUES (\'delete\', old.id, old.title, COALESCE(old.excerpt, \'\'));
                END
            ');

            DB::statement('
                CREATE TRIGGER IF NOT EXISTS notes_au AFTER UPDATE ON notes BEGIN
                    INSERT INTO notes_fts(notes_fts, rowid, title, content)
                    VALUES (\'delete\', old.id, old.title, COALESCE(old.excerpt, \'\'));
                    INSERT INTO notes_fts(rowid, title, content)
                    VALUES (new.id, new.title, COALESCE(new.excerpt, \'\'));
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

            // Recreate original triggers
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

    private function isSQLite(): bool
    {
        return config('database.default') === 'sqlite';
    }
};
