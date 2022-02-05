<?php

namespace App\Events;

use App\Models\Release;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReleaseTagged
{
    use Dispatchable, SerializesModels;

    protected $release;

    public function __construct(Release $release)
    {
        $this->release = $release;
    }

    public function getRelease(): Release
    {
        return $this->release;
    }
}
