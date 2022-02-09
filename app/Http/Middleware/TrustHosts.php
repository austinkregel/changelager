<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts()
    {
        return [
            env('APP_DOMAIN'),
            env('APP_VANITY_DOMAIN'),
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
