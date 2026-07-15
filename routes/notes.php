<?php

use App\Http\Controllers\Notes\NoteController;
use App\Http\Controllers\Notes\SearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', \App\Http\Middleware\EnsureTeamMembership::class])
    ->group(function () {
        // Notes
        Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
        Route::get('notes/create', [NoteController::class, 'create'])->name('notes.create');
        Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
        Route::get('notes/{note}', [NoteController::class, 'show'])->name('notes.show');
        Route::get('notes/{note}/edit', [NoteController::class, 'edit'])->name('notes.edit');
        Route::put('notes/{note}', [NoteController::class, 'update'])->name('notes.update');
        Route::delete('notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
        Route::patch('notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');
        Route::patch('notes/{note}/archive', [NoteController::class, 'toggleArchive'])->name('notes.archive');

        // Search
        Route::get('search', [SearchController::class, 'index'])->name('search.index');
    });
