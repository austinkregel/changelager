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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/repositories', function () {
    return Inertia::render('Repositories');
})->name('repositories');
Route::middleware(['auth:sanctum', 'verified'])->get('/repositories/{repository}', function (Repository $repository) {
    return Inertia::render('RepositoryShow', [
        'repository' => $repository,
    ]);
})->name('repositories:id');
