<?php

namespace App\Http\Controllers;

use App\Exceptions\UnauthorizedWhenCloning;
use App\Services\GitService;

class RepositoryCloneController 
{
    public function __invoke()
    {
        request()->validate([
            'url' => 'required',
        ]);
    
        $service = app(GitService::class);
        try {
            return $service->cloneRepo(request('url'));    
        } catch (UnauthorizedWhenCloning $e) {
            return response()->json([
                'message' => 'Failed to clone the repository! Please double check that the SSH key has write permissions.',
            ], 422);
        }
    }
}