<?php

namespace App\Services;

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

        $getCloneRepo = new Process(['git', 'clone', $repo, $path], null, [
            'GIT_SSH_COMMAND' => 'ssh -i "' . storage_path('ssh-key') . '" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null'
        ]);

        $getCloneRepo->run();

        if (!$getCloneRepo->isSuccessful()) {
            throw new \Exception($getCloneRepo->getErrorOutput());
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
       
        $process = new Process(['git', 'tag'], $path);

        $process->run();

        return array_values(array_filter(
            array_map('trim', explode("\n", $process->getOutput()))
        ));
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

    public function createTag($repo, string $lastRelease, string $hash)
    {
        $path = storage_path('app/' . Str::slug($repo));
        abort_if(!file_exists($path), 422, "Repository hasn't been cloned.");

        $outputCheckout = new Process(['git', 'checkout', $hash], $path);
        $outputCheckout->run();

        $outputTag = new Process(['git', 'tag', $lastRelease], $path, [
            'GIT_SSH_COMMAND' => 'ssh -i "' . storage_path('ssh-key') . '" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null'
        ]);
        $outputTag->run();
    
        $outputPush = new Process(['git', 'push', 'origin', $lastRelease, '--verbose'], $path, [
            'GIT_SSH_COMMAND' => 'ssh -i "' . storage_path('ssh-key') . '" -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null'
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