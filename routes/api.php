<?php

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

Route::post('clone', function () {
    request()->validate([
        'url' => 'required',
    ]);

    $service = app(GitService::class);

    return $service->cloneRepo(request('url'));
});

Route::post('fetch-tags', function () {
    request()->validate([
        'url' => 'required',
    ]);

    $service = app(GitService::class);

    return $service->getTags(request('url'));
});

Route::post('fetch-branches', function () {
    request()->validate([
        'url' => 'required',
    ]);

    $service = app(GitService::class);

    return $service->getBranches(request('url'));
});

Route::post('fetch-logs', function () {
    request()->validate([
        'url' => 'required',
        'release_version' => 'required|string',
        'ref' => 'required|string',
    ]);

    $service = app(GitService::class);

    return response()->json(
        $service->getLog(request('url'), request('release_version'), request('ref'))
    );
});

Route::post('create-tag', function () {
    request()->validate([
        'url' => 'required',
        'release_version' => 'required|string',
        'ref' => 'required|string',
    ]);

    $service = app(GitService::class);

    return response()->json(
        $service->createTag(request('url'), request('release_version'), request('ref'))
    );
});
