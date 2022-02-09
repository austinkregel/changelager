<?php

namespace App\Http\Controllers;

use App\Services\GitService;

class FirstCommitController
{
    public function __invoke()
    {
        request()->validate([
            'url' => 'required',
        ]);
    
        $service = app(GitService::class);
    
        return response()->json(
            $service->getFirstCommit(request('url'))
        );    
    }
}