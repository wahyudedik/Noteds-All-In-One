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
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', [$current_team]);

        $labels = $current_team->labels()
            ->withCount('notes')
            ->orderBy('name')
            ->get();

        return Inertia::render('labels/Index', [
            'labels' => $labels,
            'team' => [
                'id' => $current_team->id,
                'name' => $current_team->name,
                'slug' => $current_team->slug,
            ],
        ]);
    }

    /**
     * Store a newly created label.
     */
    public function store(StoreLabelRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', [$current_team]);

        $current_team->labels()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Label created.')]);

        return to_route('labels.index', ['current_team' => $current_team->slug]);
    }

    /**
     * Update the specified label.
     */
    public function update(StoreLabelRequest $request, Team $current_team, Label $label): RedirectResponse
    {
        Gate::authorize('update', [$label, $current_team]);

        $label->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Label updated.')]);

        return to_route('labels.index', ['current_team' => $current_team->slug]);
    }

    /**
     * Remove the specified label.
     */
    public function destroy(Team $current_team, Label $label): RedirectResponse
    {
        Gate::authorize('delete', [$label, $current_team]);

        $label->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Label deleted.')]);

        return to_route('labels.index', ['current_team' => $current_team->slug]);
    }
}
