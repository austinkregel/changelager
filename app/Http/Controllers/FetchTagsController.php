<?php

namespace App\Http\Controllers;

use App\Services\GitService;

class FetchTagsController 
{
    public function __invoke()
    {
        request()->validate([
            'url' => 'required',
        ]);
    
        $service = app(GitService::class);
    
        $tags = $service->getTags(request('url'));

        auth()->user()->repositories()->where('url', request('url'))
            ->get()
            ->each(function ($repository) use ($tags) {
                $latestTag = $tags[0] ?? null;

                if (isset($latestTag)) {
                    $repository->update([
                        'last_released_at' => $latestTag['date'],
                        'last_released_version' => $latestTag['tag'],
                    ]);
                }

                $localTags = $repository->tags()->whereIn('version', array_map(fn ($model) => $model['tag'], $tags))->get();

                foreach ($tags as $tag) {
                    if (! $localTags->contains('version', $tag['tag'])) {
                        $repository->tags()->create([
                            'version' => $tag['tag'],
                            'notes' => $tag['notes'],
                            'released_at' => $tag['date'],
                        ]);
                    }
                }
            });

        return $tags;
    }
}