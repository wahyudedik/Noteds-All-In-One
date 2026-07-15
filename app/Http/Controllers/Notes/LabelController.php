<?php

namespace App\Http\Controllers\Notes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\StoreLabelRequest;
use App\Models\Label;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LabelController extends Controller
{
    /**
     * Display a listing of labels for the current team.
     */
    public function index(Request $request, Team $team): Response
    {
        Gate::authorize('viewAny', [Label::class, $team]);

        $labels = $team->labels()
            ->withCount('notes')
            ->orderBy('name')
            ->get();

        return Inertia::render('labels/Index', [
            'labels' => $labels,
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'slug' => $team->slug,
            ],
        ]);
    }

    /**
     * Store a newly created label.
     */
    public function store(StoreLabelRequest $request, Team $team): RedirectResponse
    {
        Gate::authorize('create', [Label::class, $team]);

        $team->labels()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Label created.')]);

        return to_route('labels.index', ['current_team' => $team->slug]);
    }

    /**
     * Update the specified label.
     */
    public function update(StoreLabelRequest $request, Team $team, Label $label): RedirectResponse
    {
        Gate::authorize('update', [$label, $team]);

        $label->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Label updated.')]);

        return to_route('labels.index', ['current_team' => $team->slug]);
    }

    /**
     * Remove the specified label.
     */
    public function destroy(Team $team, Label $label): RedirectResponse
    {
        Gate::authorize('delete', [$label, $team]);

        $label->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Label deleted.')]);

        return to_route('labels.index', ['current_team' => $team->slug]);
    }
}
