<?php

use App\Models\Repository;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('web')->get('/{version?}', function ($repositoryIdentifier, $version = null) {
    $repository = Repository::where('slug', $repositoryIdentifier)->firstOrFail();

    abort_unless($repository->is_public, 404);

    $repository->load(['releases' => fn ($query) => $query->orderBy('released_at', 'desc')]);
    
    return Inertia::render('Repositories/Public', [
        'repository' => $repository,
    ]);
})->name('public:id');
