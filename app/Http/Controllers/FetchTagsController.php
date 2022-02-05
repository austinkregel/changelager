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

                $localTags = $repository->releases()->whereIn('version', $tags->map(fn ($model) => $model['tag']))->get();

                foreach ($tags as $tag) {
                    if (empty($tag['tag'])) {
                        continue;
                    }

                    if (! $localTags->contains('version', $tag['tag'])) {
                        $localTag = $repository->releases()->firstOrNew([
                            'version' => $tag['tag'],
                            'hash' =>  $tag['hash'] ?? null,
                        ], [
                            'version' => $tag['tag'],
                            'notes' => $tag['notes'] ?? $tag['hash'] ?? null,
                            'released_at' => $tag['date'],
                        ]);

                        $localTag->fill([
                            'version' => $tag['tag'],
                            'notes' => $tag['notes'] ?? null,
                            'released_at' => $tag['date'],
                            'hash' =>  $tag['hash'] ?? null,
                        ]);

                        $localTag->save();
                    }
                }
            });

        return $tags;
    }
}