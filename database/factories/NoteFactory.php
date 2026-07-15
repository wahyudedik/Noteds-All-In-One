<?php

namespace Database\Factories;

use App\Enums\NoteType;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(NoteType::cases());

        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'content' => $this->generateContent($type),
            'excerpt' => fake()->paragraph(1),
            'type' => $type,
            'is_pinned' => fake()->boolean(10),
            'is_archived' => false,
            'category_id' => null,
        ];
    }

    /**
     * Generate content based on note type.
     */
    private function generateContent(NoteType $type): array
    {
        return match ($type) {
            NoteType::Text => [
                'html' => '<p>' . fake()->paragraph(3) . '</p>',
                'text' => fake()->paragraph(3),
            ],
            NoteType::Checklist => [
                'items' => collect(range(1, fake()->numberBetween(3, 8)))
                    ->map(fn () => [
                        'text' => fake()->sentence(3),
                        'checked' => fake()->boolean(30),
                    ])
                    ->toArray(),
            ],
            NoteType::List => [
                'items' => collect(range(1, fake()->numberBetween(3, 6)))
                    ->map(fn () => fake()->sentence(3))
                    ->toArray(),
            ],
            NoteType::Code => [
                'code' => "// Sample code\nfunction hello() {\n  console.log('Hello, World!');\n}\n\nhello();",
                'language' => fake()->randomElement(['javascript', 'typescript', 'php', 'python']),
            ],
        };
    }

    /**
     * Set the note type to text.
     */
    public function text(): static
    {
        return $this->state(fn () => [
            'type' => NoteType::Text,
            'content' => [
                'html' => '<p>' . fake()->paragraph(3) . '</p>',
                'text' => fake()->paragraph(3),
            ],
        ]);
    }

    /**
     * Set the note type to checklist.
     */
    public function checklist(): static
    {
        return $this->state(fn () => [
            'type' => NoteType::Checklist,
            'content' => [
                'items' => collect(range(1, 5))
                    ->map(fn () => [
                        'text' => fake()->sentence(3),
                        'checked' => fake()->boolean(30),
                    ])
                    ->toArray(),
            ],
        ]);
    }

    /**
     * Set the note as pinned.
     */
    public function pinned(): static
    {
        return $this->state(fn () => ['is_pinned' => true]);
    }

    /**
     * Set the note as archived.
     */
    public function archived(): static
    {
        return $this->state(fn () => ['is_archived' => true]);
    }
}
