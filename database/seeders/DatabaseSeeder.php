<?php

namespace Database\Seeders;

use App\Enums\NoteType;
use App\Models\Category;
use App\Models\Label;
use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Noteds',
            'email' => 'info@noteds.com',
        ]);

        // Get or create the user's personal team
        $team = $user->ownedTeams()->first();
        if (! $team) {
            $team = $user->ownedTeams()->create([
                'name' => $user->name . "'s Team",
                'is_personal' => true,
            ]);
        }

        // Set current_team_id so Inertia shared data (currentTeam) is not null
        if (! $user->current_team_id) {
            $user->switchTeam($team);
        }

        // ── Categories ──────────────────────────────────────
        $categories = collect([
            ['name' => 'Pekerjaan', 'color' => '#3B82F6', 'icon' => 'briefcase'],
            ['name' => 'Pribadi', 'color' => '#22C55E', 'icon' => 'user'],
            ['name' => 'Keuangan', 'color' => '#EAB308', 'icon' => 'wallet'],
            ['name' => 'Proyek', 'color' => '#8B5CF6', 'icon' => 'folder'],
            ['name' => 'Ide & Brainstorm', 'color' => '#EC4899', 'icon' => 'lightbulb'],
        ])->map(fn ($cat) => $team->categories()->create($cat));

        // ── Labels ──────────────────────────────────────────
        $labels = collect([
            ['name' => 'Penting', 'color' => '#EF4444'],
            ['name' => 'Draft', 'color' => '#F97316'],
            ['name' => 'Review', 'color' => '#EAB308'],
            ['name' => 'Selesai', 'color' => '#22C55E'],
            ['name' => 'Referensi', 'color' => '#06B6D4'],
            ['name' => 'Ide', 'color' => '#8B5CF6'],
            ['name' => 'Mendesak', 'color' => '#DC2626'],
            ['name' => 'Rencana', 'color' => '#6366F1'],
        ])->map(fn ($label) => $team->labels()->create($label));

        // ── Notes ───────────────────────────────────────────
        $notesData = [
            [
                'title' => 'Rencana Meeting Mingguan',
                'type' => NoteType::Checklist,
                'content' => [
                    'items' => [
                        ['text' => 'Review progress proyek Q3', 'checked' => true],
                        ['text' => 'Diskusi timeline rilis', 'checked' => false],
                        ['text' => 'Assign task baru', 'checked' => false],
                        ['text' => 'Review budget', 'checked' => false],
                    ],
                ],
                'excerpt' => 'Review progress proyek Q3, diskusi timeline rilis, assign task baru, review budget',
                'category_id' => $categories[0]->id,
                'is_pinned' => true,
            ],
            [
                'title' => 'Catatan Belanja Bulanan',
                'type' => NoteType::List,
                'content' => [
                    'items' => ['Sembako - Rp 500.000', 'Listrik - Rp 350.000', 'Internet - Rp 200.000', 'Transport - Rp 300.000'],
                ],
                'excerpt' => 'Sembako, Listrik, Internet, Transport',
                'category_id' => $categories[2]->id,
                'is_pinned' => false,
            ],
            [
                'title' => 'API Documentation Notes',
                'type' => NoteType::Code,
                'content' => [
                    'code' => "// GET /api/notes\n// Response: { data: Note[] }\n\nfetch('/api/notes')\n  .then(res => res.json())\n  .then(data => console.log(data));",
                    'language' => 'javascript',
                ],
                'excerpt' => 'GET /api/notes Response: data Note[]',
                'category_id' => $categories[3]->id,
                'is_pinned' => false,
            ],
            [
                'title' => 'Ide Aplikasi Baru',
                'type' => NoteType::Text,
                'content' => [
                    'html' => '<h2>Dashboard Widget</h2><p>Buat dashboard yang bisa di-custom dengan widget-widget seperti:</p><ul><li>Quick notes</li><li>Todo list</li><li>Calendar view</li><li>Weather widget</li></ul><p>Target: MVP dalam 2 minggu.</p>',
                    'text' => 'Dashboard Widget\nBuat dashboard yang bisa di-custom dengan widget-widget seperti: Quick notes, Todo list, Calendar view, Weather widget.\nTarget: MVP dalam 2 minggu.',
                ],
                'excerpt' => 'Buat dashboard yang bisa di-custom dengan widget-widget seperti Quick notes, Todo list, Calendar view, Weather widget.',
                'category_id' => $categories[4]->id,
                'is_pinned' => true,
            ],
            [
                'title' => 'Status Proyek Noteds',
                'type' => NoteType::Text,
                'content' => [
                    'html' => '<h3>Progress</h3><p>Phase 1 (Fondasi) sudah selesai. Phase 2 (Core Notes) sedang dalam pengerjaan.</p><p><strong>Fitur yang sudah jadi:</strong></p><ul><li>Auth & Team Management</li><li>Dashboard</li><li>Dark/Light Mode</li></ul>',
                    'text' => 'Progress\nPhase 1 (Fondasi) sudah selesai. Phase 2 (Core Notes) sedang dalam pengerjaan.\nFitur yang sudah jadi: Auth & Team Management, Dashboard, Dark/Light Mode',
                ],
                'excerpt' => 'Phase 1 (Fondasi) sudah selesai. Phase 2 (Core Notes) sedang dalam pengerjaan.',
                'category_id' => $categories[3]->id,
                'is_pinned' => false,
            ],
            [
                'title' => 'Resep Masakan Favorit',
                'type' => NoteType::List,
                'content' => [
                    'items' => ['Nasi Goreng Spesial', 'Mie Ayam Bakso', 'Soto Ayam Lamongan', 'Rendang Padang', 'Gado-gado Jakarta'],
                ],
                'excerpt' => 'Nasi Goreng Spesial, Mie Ayam Bakso, Soto Ayam Lamongan, Rendang Padang, Gado-gado Jakarta',
                'category_id' => $categories[1]->id,
                'is_pinned' => false,
            ],
            [
                'title' => 'Database Migration Checklist',
                'type' => NoteType::Checklist,
                'content' => [
                    'items' => [
                        ['text' => 'Create notes table', 'checked' => true],
                        ['text' => 'Create categories table', 'checked' => true],
                        ['text' => 'Create labels table', 'checked' => true],
                        ['text' => 'Create pivot tables', 'checked' => true],
                        ['text' => 'Setup FTS5', 'checked' => false],
                        ['text' => 'Run seeders', 'checked' => false],
                    ],
                ],
                'excerpt' => 'Create notes table, Create categories table, Create labels table, Create pivot tables, Setup FTS5, Run seeders',
                'category_id' => $categories[3]->id,
                'is_pinned' => false,
            ],
            [
                'title' => 'Tips Productivity',
                'type' => NoteType::Text,
                'content' => [
                    'html' => '<ol><li><strong>Pomodoro Technique</strong> - Kerja 25 menit, istirahat 5 menit</li><li><strong>Two-Minute Rule</strong> - Jika bisa diselesaikan dalam 2 menit, langsung lakukan</li><li><strong>Eisenhower Matrix</strong> - Prioritaskan berdasarkan urgent & important</li><li><strong>Time Blocking</strong> - Blokir waktu khusus untuk deep work</li></ol>',
                    'text' => 'Tips Productivity:\n1. Pomodoro Technique - Kerja 25 menit, istirahat 5 menit\n2. Two-Minute Rule - Jika bisa diselesaikan dalam 2 menit, langsung lakukan\n3. Eisenhower Matrix - Prioritaskan berdasarkan urgent & important\n4. Time Blocking - Blokir waktu khusus untuk deep work',
                ],
                'excerpt' => 'Pomodoro Technique, Two-Minute Rule, Eisenhower Matrix, Time Blocking',
                'category_id' => $categories[1]->id,
                'is_pinned' => false,
            ],
        ];

        foreach ($notesData as $noteData) {
            $note = Note::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'title' => $noteData['title'],
                'content' => $noteData['content'],
                'excerpt' => $noteData['excerpt'],
                'type' => $noteData['type'],
                'is_pinned' => $noteData['is_pinned'] ?? false,
                'is_archived' => false,
                'category_id' => $noteData['category_id'] ?? null,
            ]);

            // Attach random labels
            $randomLabels = $labels->random(rand(1, 3))->pluck('id')->toArray();
            $note->labels()->sync($randomLabels);
        }
    }
}
