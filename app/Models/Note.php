<?php

namespace App\Models;

use App\Enums\NoteType;
use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string $title
 * @property array $content
 * @property string $excerpt
 * @property NoteType $type
 * @property bool $is_pinned
 * @property bool $is_archived
 * @property int|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Team $team
 * @property-read User $author
 * @property-read Category|null $category
 * @property-read Collection<int, Label> $labels
 */
class Note extends Model
{
    /** @use HasFactory<NoteFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'title',
        'content',
        'excerpt',
        'type',
        'is_pinned',
        'is_archived',
        'category_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_pinned' => 'boolean',
            'is_archived' => 'boolean',
            'type' => NoteType::class,
        ];
    }

    /**
     * Get the team that owns the note.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the author of the note.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category for the note.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the labels for the note.
     *
     * @return BelongsToMany<Label, $this>
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_note');
    }

    // ── Scopes ──────────────────────────────────────────────

    /**
     * Scope notes to a specific team.
     */
    public function scopeForTeam($query, Team $team)
    {
        return $query->where('team_id', $team->id);
    }

    /**
     * Scope to pinned notes only.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to non-archived notes only.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }


    /**
     * Scope to notes in a specific category.
     */
    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to notes with a specific label.
     */
    public function scopeWithLabel($query, int $labelId)
    {
        return $query->whereHas('labels', fn ($q) => $q->where('labels.id', $labelId));
    }

    /**
     * Scope to notes of a specific type.
     */
    public function scopeOfType($query, NoteType $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to search notes by term (text search).
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
                ->orWhere('excerpt', 'LIKE', "%{$term}%");
        });
    }

    // ── Methods ─────────────────────────────────────────────

    /**
     * Generate an excerpt from the note content.
     */
    public function generateExcerpt(): void
    {
        $text = match ($this->type) {
            NoteType::Text => $this->content['text'] ?? '',
            NoteType::Checklist => collect($this->content['items'] ?? [])
                ->pluck('text')
                ->implode(' '),
            NoteType::List => collect($this->content['items'] ?? [])
                ->implode(' '),
            NoteType::Code => $this->content['code'] ?? '',
        };

        $this->attributes['excerpt'] = Str::limit(strip_tags($text), 300);
    }
}
