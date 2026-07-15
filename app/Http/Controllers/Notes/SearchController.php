<?php

namespace App\Http\Controllers\Notes;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    /**
     * Search notes for the current team.
     */
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', [$current_team]);

        $query = Note::forTeam($current_team)
            ->notArchived()
            ->with(['category', 'labels', 'author']);

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->search($term);
        }

        if ($request->filled('type')) {
            $query->ofType($request->input('type'));
        }

        if ($request->filled('category_id')) {
            $query->inCategory($request->input('category_id'));
        }

        if ($request->filled('label_id')) {
            $query->withLabel($request->input('label_id'));
        }

        $notes = $query->latest('updated_at')->paginate(20)->withQueryString();

        $categories = $current_team->categories()->orderBy('name')->get();
        $labels = $current_team->labels()->orderBy('name')->get();

        return Inertia::render('notes/Search', [
            'notes' => $notes,
            'categories' => $categories,
            'labels' => $labels,
            'filters' => $request->only(['q', 'type', 'category_id', 'label_id']),
            'team' => [
                'id' => $current_team->id,
                'name' => $current_team->name,
                'slug' => $current_team->slug,
            ],
        ]);
    }
}
