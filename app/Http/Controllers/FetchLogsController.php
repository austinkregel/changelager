<?php

namespace App\Http\Controllers;

use App\Services\GitService;

class FetchLogsController
{
    public function __invoke()
    {
        request()->validate([
            'url' => 'required',
            'release_version' => 'required|string',
            'ref' => 'required|string',
        ]);
    
        $service = app(GitService::class);
    
        return response()->json(
            $service->getLog(request('url'), request('release_version'), request('ref'))
        );    
    }
}