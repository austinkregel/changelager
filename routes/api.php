<?php

use App\Http\Controllers\CreateTagController;
use App\Http\Controllers\FetchBranchesController;
use App\Http\Controllers\FetchLogsController;
use App\Http\Controllers\FetchTagsController;
use App\Http\Controllers\RepositoryCloneController;
use App\Http\Controllers\RepositoryController;
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
Route::post('clone', RepositoryCloneController::class);
Route::post('fetch-tags', FetchTagsController::class);
Route::post('fetch-branches', FetchBranchesController::class);
Route::post('fetch-logs', FetchLogsController::class);
Route::post('create-tag', CreateTagController::class);
