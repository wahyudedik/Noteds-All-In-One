<?php

namespace App\Http\Middleware;

use App\Data\UserTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentTeam' => fn () => $this->resolveCurrentTeam($user),
            'teams' => fn () => $user?->toUserTeams(includeCurrent: true) ?? [],
        ];
    }

    /**
     * Resolve the current team for the user.
     *
     * Falls back to the user's personal team when currentTeam is null,
     * ensuring the frontend always receives a valid team object.
     */
    protected function resolveCurrentTeam(?User $user): ?UserTeam
    {
        if (! $user) {
            return null;
        }

        $team = $user->currentTeam ?? $user->personalTeam();

        if (! $team) {
            return null;
        }

        // Auto-set current_team_id if it was not set
        if (! $user->current_team_id) {
            $user->switchTeam($team);
        }

        return $user->toUserTeam($team);
    }
}
