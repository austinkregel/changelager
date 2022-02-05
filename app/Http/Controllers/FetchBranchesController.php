<?php

namespace App\Http\Controllers;

use App\Services\GitService;

class FetchBranchesController 
{
    public function __invoke()
    {
        request()->validate([
            'url' => 'required',
        ]);
    
        $service = app(GitService::class);
    
        return $service->getBranches(request('url'));    
    }
}