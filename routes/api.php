<?php

use App\Http\Controllers\CreateTagController;
use App\Http\Controllers\FetchBranchesController;
use App\Http\Controllers\FetchLogsController;
use App\Http\Controllers\FetchTagsController;
use App\Http\Controllers\RepositoryCloneController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\FirstCommitController;
use App\Services\GitService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('repositories', RepositoryController::class);
Route::middleware('hasRole:releaser')->post('clone', RepositoryCloneController::class);
Route::middleware('hasRole:releaser')->post('fetch-tags', FetchTagsController::class);
Route::middleware('hasRole:releaser')->post('fetch-branches', FetchBranchesController::class);
Route::middleware('hasRole:releaser')->post('fetch-logs', FetchLogsController::class);
Route::middleware('hasRole:releaser')->post('create-tag', CreateTagController::class);
Route::middleware('hasRole:releaser')->post('first-commit', FirstCommitController::class);
