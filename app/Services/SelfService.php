<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SelfService
{
    public function version()
    {
        if (cache()->has('version')) {
            return cache('version');
        }

        if (config('app.debug')) {
            return cache()->remember('version', now()->addMinute(), fn () => $this->fetchCurrentCommentHash());
        }

        return cache()->remember('version', now()->addMinute(), fn () => $this->fetchLatestTag());
    }

    protected function fetchCurrentCommentHash()
    {
        $process = new Process(['git', 'rev-parse', '--short', 'HEAD']);
        $process->run();

        return trim($process->getOutput());
    }

    public function fetchLatestTag()
    {
        $process = new Process(['git', 'log', '--tags', '--simplify-by-decoration', '--pretty=%ai %d  %H'], base_path());

        $process->run();

        return Str::of($process->getOutput())
        ->explode("\n")
        ->map('trim')
        ->flatMap(function ($row) {
            $formattedRow = explode('  ', $row);
            $tag = $formattedRow[1] ?? "";
            preg_match('#\((.*?)\)#', $tag, $match);
            $potentialTag = ($match[1] ?? "");
            $hash = trim(end($formattedRow));

            return Str::of($potentialTag)->explode(", ")->filter()->filter(fn($rawRefName) => Stringable::startsWith($rawRefName, 'tag: '))->map(function ($rawRefName) {
                    return Str::substr($rawRefName, 5);
                
                return $rawRefName;
            })->values()->map(fn ($tag) => [
                'date' => Carbon::parse(trim($formattedRow[0], '"'))->format('Y-m-d'),
                'tag' => $tag,
                'hash' => $hash,
            ])->filter(fn($tag) => !empty($tag['tag']));
        })
        ->filter()
        ->values()
        ->sortByDesc(function(array $tag) {
            return $tag['date'];
        })->first();
    }   
}