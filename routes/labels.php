<?php

use App\Http\Controllers\Notes\LabelController;
use Illuminate\Support\Facades\Route;

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', \App\Http\Middleware\EnsureTeamMembership::class])
    ->group(function () {
        Route::get('labels', [LabelController::class, 'index'])->name('labels.index');
        Route::post('labels', [LabelController::class, 'store'])->name('labels.store');
        Route::put('labels/{label}', [LabelController::class, 'update'])->name('labels.update');
        Route::delete('labels/{label}', [LabelController::class, 'destroy'])->name('labels.destroy');
    });
