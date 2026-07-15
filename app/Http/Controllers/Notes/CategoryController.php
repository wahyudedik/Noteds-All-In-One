<?php

namespace App\Http\Controllers\Notes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for the current team.
     */
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', [$current_team]);

        $categories = $current_team->categories()
            ->withCount('notes')
            ->orderBy('name')
            ->get();

        return Inertia::render('categories/Index', [
            'categories' => $categories,
            'team' => [
                'id' => $current_team->id,
                'name' => $current_team->name,
                'slug' => $current_team->slug,
            ],
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', [$current_team]);

        $current_team->categories()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category created.')]);

        return to_route('categories.index', ['current_team' => $current_team->slug]);
    }

    /**
     * Update the specified category.
     */
    public function update(StoreCategoryRequest $request, Team $current_team, Category $category): RedirectResponse
    {
        Gate::authorize('update', [$category, $current_team]);

        $category->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category updated.')]);

        return to_route('categories.index', ['current_team' => $current_team->slug]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Team $current_team, Category $category): RedirectResponse
    {
        Gate::authorize('delete', [$category, $current_team]);

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category deleted.')]);

        return to_route('categories.index', ['current_team' => $current_team->slug]);
    }
}
