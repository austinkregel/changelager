<?php

namespace App\Http\Controllers;

use App\Services\GitService;

class CreateTagController
{
    public function __invoke()
    {
        request()->validate([
            'url' => 'required',
            'release_version' => 'required|string',
            'ref' => 'required|string',
        ]);
    
        $service = app(GitService::class);

        $repository = auth()->user()->repositories()->whereUrl(request('url'))->first();
        $service->createTag(request('url'), request('release_version'), request('ref'));
        
        $tag = $repository->releases()->create([
            'version' => request('release_version'),
            'hash' => request('ref'),
            'notes' => request('body'),
            'released_at' => now(),
        ]);

        return response()->json($tag);    
    }
}