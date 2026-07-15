<?php

namespace App\Http\Controllers\Notes;

use App\Enums\NoteType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\StoreNoteRequest;
use App\Http\Requests\Notes\UpdateNoteRequest;
use App\Models\Category;
use App\Models\Label;
use App\Models\Note;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    /**
     * Display a listing of notes for the current team.
     */
    public function index(Request $request, Team $team): Response
    {
        Gate::authorize('viewAny', [Note::class, $team]);

        $query = Note::forTeam($team)
            ->with(['category', 'labels', 'author'])
            ->latest('is_pinned')
            ->latest('updated_at');

        // Show archived notes or filter to non-archived only
        if (! $request->boolean('archived')) {
            $query->notArchived();
        }

        // Filter by type
        if ($request->filled('type') && NoteType::tryFrom($request->input('type'))) {
            $query->ofType(NoteType::from($request->input('type')));
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->inCategory($request->input('category_id'));
        }

        // Filter by label
        if ($request->filled('label_id')) {
            $query->withLabel($request->input('label_id'));
        }

        // Search
        if ($request->filled('q')) {
            $query->search($request->input('q'));
        }

        $notes = $query->paginate(20)->withQueryString();

        $categories = $team->categories()->orderBy('name')->get();
        $labels = $team->labels()->orderBy('name')->get();

        return Inertia::render('notes/Index', [
            'notes' => $notes,
            'categories' => $categories,
            'labels' => $labels,
            'filters' => $request->only(['type', 'category_id', 'label_id', 'q', 'archived']),
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ],
        ]);
    }

    /**
     * Show the form for creating a new note.
     */
    public function create(Request $request, Team $team): Response
    {
        Gate::authorize('create', [Note::class, $team]);

        $categories = $team->categories()->orderBy('name')->get();
        $labels = $team->labels()->orderBy('name')->get();

        return Inertia::render('notes/Create', [
            'categories' => $categories,
            'labels' => $labels,
            'types' => collect(NoteType::cases())->map(fn (NoteType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
                'icon' => $type->icon(),
                'defaultContent' => $type->defaultContent(),
            ]),
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ],
        ]);
    }

    /**
     * Store a newly created note.
     */
    public function store(StoreNoteRequest $request, Team $team): RedirectResponse
    {
        Gate::authorize('create', [Note::class, $team]);

        $note = Note::create([
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'type' => NoteType::from($request->validated('type')),
            'category_id' => $request->validated('category_id'),
        ]);

        $note->generateExcerpt();
        $note->save();

        // Sync labels
        if ($request->has('label_ids')) {
            $note->labels()->sync($request->validated('label_ids'));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Note created.')]);

        return to_route('notes.show', ['current_team' => $team->slug, 'note' => $note->id]);
    }

    /**
     * Display the specified note.
     */
    public function show(Team $team, Note $note): Response
    {
        Gate::authorize('view', [$note, $team]);

        $note->load(['category', 'labels', 'author']);

        return Inertia::render('notes/Show', [
            'note' => $note,
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(Team $team, Note $note): Response
    {
        Gate::authorize('update', [$note, $team]);

        $note->load(['labels']);

        $categories = $team->categories()->orderBy('name')->get();
        $labels = $team->labels()->orderBy('name')->get();

        return Inertia::render('notes/Edit', [
            'note' => $note,
            'categories' => $categories,
            'labels' => $labels,
            'types' => collect(NoteType::cases())->map(fn (NoteType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
                'icon' => $type->icon(),
            ]),
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ],
        ]);
    }

    /**
     * Update the specified note.
     */
    public function update(UpdateNoteRequest $request, Team $team, Note $note): RedirectResponse
    {
        Gate::authorize('update', [$note, $team]);

        $note->update([
            'title' => $request->validated('title', $note->title),
            'content' => $request->validated('content', $note->content),
            'type' => NoteType::tryFrom($request->validated('type', $note->type->value)) ?? $note->type,
            'category_id' => $request->validated('category_id', $note->category_id),
        ]);

        $note->generateExcerpt();
        $note->save();

        // Sync labels
        if ($request->has('label_ids')) {
            $note->labels()->sync($request->validated('label_ids'));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Note updated.')]);

        return to_route('notes.show', ['current_team' => $team->slug, 'note' => $note->id]);
    }

    /**
     * Remove the specified note.
     */
    public function destroy(Team $team, Note $note): RedirectResponse
    {
        Gate::authorize('delete', [$note, $team]);

        $note->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Note deleted.')]);

        return to_route('notes.index', ['current_team' => $team->slug]);
    }

    /**
     * Toggle the pinned status of the note.
     */
    public function togglePin(Team $team, Note $note): RedirectResponse
    {
        Gate::authorize('update', [$note, $team]);

        $note->update(['is_pinned' => !$note->is_pinned]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $note->is_pinned ? __('Note pinned.') : __('Note unpinned.'),
        ]);

        return back();
    }

    /**
     * Toggle the archived status of the note.
     */
    public function toggleArchive(Team $team, Note $note): RedirectResponse
    {
        Gate::authorize('update', [$note, $team]);

        $note->update(['is_archived' => !$note->is_archived]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $note->is_archived ? __('Note archived.') : __('Note restored.'),
        ]);

        return back();
    }
}
