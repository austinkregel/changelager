<?php

namespace App\Http\Controllers;

use App\Models\Repository;
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
            ->each(function (Repository $repository) use ($tags) {
                $latestTag = $tags[0] ?? null;

                if (isset($latestTag)) {
                    $repository->update([
                        'last_released_at' => $latestTag['date'],
                        'last_released_version' => $latestTag['tag'],
                    ]);
                }

                $localTags = $repository->releases()->whereIn('version', array_map(fn ($model) => $model['tag'], $tags))->get();

                foreach ($tags as $tag) {
                    if (! $localTags->contains('version', $tag['tag'])) {
                        $tag = $repository->releases()->firstOrNew([
                            'version' => $tag['tag'],
                        ], [
                            'version' => $tag['tag'],
                            'notes' => $tag['notes'],
                            'released_at' => $tag['date'],
                        ]);

                        $tag->fill([
                            'version' => $tag['tag'],
                            'notes' => $tag['notes'],
                            'released_at' => $tag['date'],
                        ])

                        $tag->save();
                    }
                }
            });

        return $tags;
    }
}