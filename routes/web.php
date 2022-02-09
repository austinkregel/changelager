<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\Repository;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => redirect('/login'))->middleware(RedirectIfAuthenticated::class);

Route::middleware(['auth:sanctum', 'verified'])->get('/repositories', function () {
    return Inertia::render('Repositories/Index', [
        'repositories' => auth()->user()->repositories()->with(['releases' => fn ($query) => $query->orderBy('released_at', 'desc')])->get()->toArray()
    ]);
})->name('repositories');

Route::middleware(['auth:sanctum', 'verified', 'hasRole:releaser'])->get('/repositories/new', function () {
    return Inertia::render('Repositories/Create');
})->name('repositories:new');

Route::middleware(['auth:sanctum', 'verified', 'hasRole:releaser'])->get('/repositories/{repository}', function (Repository $repository) {
    $repository->load(['releases' => fn ($query) => $query->orderBy('released_at', 'desc')]);
    return Inertia::render('Repositories/Show', [
        'repository' => $repository,
    ]);
})->name('repositories:id');
