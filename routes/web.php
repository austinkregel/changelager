<?php

use App\Http\Middleware\RedirectIfAuthenticated;
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

Route::middleware(['auth:sanctum', 'verified'])->get('/repositories/new', function () {
    return Inertia::render('Repositories/Create');
})->name('repositories:new');

Route::middleware(['auth:sanctum', 'verified'])->get('/repositories/{repository}', function (Repository $repository) {
    $repository->load('releases');
    return Inertia::render('Repositories/Show', [
        'repository' => $repository,
    ]);
})->name('repositories:id');
