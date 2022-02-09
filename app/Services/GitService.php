<?php

namespace App\Services;

use App\Exceptions\UnauthorizedWhenCloning;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class GitService
{
    public function __construct() { }

    public function cloneRepoAndFetchLog(string $repo, string $newReleaseVersion, string $currentMostRef): array
    {
        try {
            $this->cloneRepo($repo);

            $log = $this->getLog($repo, $newReleaseVersion, $currentMostRef);

            return $log;
        } finally {
            $this->removeRepo($repo);
        }
    }

    public function cloneRepoAndFetchTags(string $repoUrl): array
    {
        try {
            $this->cloneRepo($repoUrl);

            return $this->getTags($repoUrl);
        } finally {
            $this->removeRepo($repoUrl);
        }
    }
    
    public function cloneRepo(string $repo)
    {
        $path = storage_path('app/' . Str::slug($repo));

        if (file_exists($path)) {
            $this->removeRepo($repo);
        }

        if (str_starts_with($repo, 'https://') || str_starts_with($repo, 'http://')) {
            $parts = parse_url($repo);
            $repo = sprintf('git@%s:%s', $parts['host'], trim($parts['path'], '/'));
        }

        $getCloneRepo = new Process(['git', 'clone', $repo, $path], null, [
            'GIT_SSH_COMMAND' => 'ssh -i "' . $path  . '.pem" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null'
        ]);

        $getCloneRepo->run();

        if (!$getCloneRepo->isSuccessful()) {
            throw new UnauthorizedWhenCloning($getCloneRepo->getErrorOutput());
        }
    }

    protected function removeRepo($repo)
    {
        $path = storage_path('app/' . Str::slug($repo));

        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");

        $deleteRepo = new Process(['rm', '-rf', $path]);

        $deleteRepo->run();
    }

    public function getTags($url)
    {
        $path = storage_path('app/' . Str::slug($url));
        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");
       
        $process = new Process(['git', 'log', '--tags', '--simplify-by-decoration', '--pretty=%ai %d  %H'], $path);

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

            return Str::of($potentialTag)->explode(", ")->filter()->filter(fn($rawRefName) => Str::startsWith($rawRefName, 'tag: '))->map(function ($rawRefName) {
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
        });
    }

    public function getBranches($url)
    {
        $path = storage_path('app/' . Str::slug($url));
        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");
      
        $process = new Process(['git', 'branch', '-a'], $path);

        $process->run();

        $branchesAndHashes = [];

        $branches = array_filter(array_map(fn($row) => trim($row, " \t\n\r\0\x0B\*"), explode("\n", $process->getOutput())));

        foreach ($branches as $branch) {
            $process = new Process(['git', 'rev-parse', $branch], $path);

            $process->run();

            $branchesAndHashes[] = [
                'name' => $branch,
                'hash' => trim($process->getOutput())
            ];
        }
        return $branchesAndHashes;
    }

    public function getLog($repo, string $lastRelease, string $currentMostRef): array
    {
        $path = storage_path('app/' . Str::slug($repo));
        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");
        
        $innerPart = '🔤';
        $outerPart = '🚱';
        $formatingParts = [
            's', // subject
            'ae', // author email
            'b' // commit body
        ];

        $format = $innerPart . '%' . implode($innerPart . '%', $formatingParts) . $outerPart;

        $getLog = new Process(['git', 'rev-list', "--pretty=format:$format", "--header", $lastRelease . '...' . ($currentMostRef ?? 'HEAD')], $path);

        $getLog->run();

        $output = $getLog->getOutput();

        $parts =  array_values(array_filter(array_map(fn ($part) => trim($part, " \n\t\r\0\x0B\""), explode($outerPart, $output)), function ($part) {
            return !empty(trim($part));
        }));

        return array_map(function ($part) use ($innerPart, $outerPart, $repo, $formatingParts, $output) {
            $splitted = array_map(function ($detail) {
                return trim($detail, " \n\t\r\0\x0B\"");
            }, array_filter(explode($innerPart, $part), function ($bit) {
                return !empty($bit);
            }));

            if (count($splitted) < 3) {
                dd($splitted, $part, $output);
            }

            list($bithash, $description, $author) = $splitted;

            list($_, $hash) = explode(' ', $bithash);

            $hash = trim($hash);
            
            return compact('hash', 'description', 'author');
        }, $parts);
    }

    public function getFirstCommit($repo)
    {
        $path = storage_path('app/' . Str::slug($repo));
        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");
        
        $getLog = new Process(['git', 'rev-list', "--max-parents=0", 'HEAD'], $path);

        $getLog->run();

        return trim($getLog->getOutput());
    }

    public function createTag($repo, string $lastRelease, string $hash)
    {
        $path = storage_path('app/' . Str::slug($repo));
        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");

        $outputCheckout = new Process(['git', 'checkout', $hash], $path);
        $outputCheckout->run();

        $outputTag = new Process(['git', 'tag', $lastRelease], $path, [
            'GIT_SSH_COMMAND' => 'ssh -i "' . $path  . '.pem" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null',
            'GIT_COMMITTER_NAME' => 'Changelager Bot',
            'GIT_COMMITTER_EMAIL' => 'lager@changed.to'
        ]);
        $outputTag->run();
    
        $outputPush = new Process(['git', 'push', 'origin', $lastRelease, '--verbose'], $path, [
            'GIT_SSH_COMMAND' => 'ssh -i "' . $path  . '.pem" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null'
        ]);
        $outputPush->run();

        info('Tag created: ' . $lastRelease, [
            'checkout_out' => $outputCheckout->getOutput(),
            'checkout_err' => $outputCheckout->getErrorOutput(),
            'tag_out' => $outputTag->getOutput(),
            'tag_err' => $outputTag->getErrorOutput(),
            'push_out' => $outputPush->getOutput(),
            'push_err' => $outputPush->getErrorOutput(),
        ]);

        abort_if(stripos($outputTag->getErrorOutput(), "tag '{$lastRelease}' already exists") !== false, 422, 'Tag already exists!');

        return '';
    }
}