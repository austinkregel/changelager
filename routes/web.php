<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\PublicRepositoryMiddleware;
use App\Models\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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

Route::middleware([PublicRepositoryMiddleware::class])->domain('{repoIdentifier}.'.config('app.vanity_domain'))->get('/{version?}', function ($repositoryIdentifier, $version = null) {
    $repository = Repository::where('slug', $repositoryIdentifier)->firstOrFail();

    $repository->load(['releases' => fn ($query) => $query->orderBy('released_at', 'desc')]);
    
    return Inertia::render('Repositories/Public', [
        'repository' => $repository,
    ]);
})->name('public:id');
