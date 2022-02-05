<?php

namespace App\Listeners;

use App\Events\ReleaseTagged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateTagAndLastReleaseOnRepository
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ReleaseTagged  $event
     * @return void
     */
    public function handle(ReleaseTagged $event)
    {
        $release = $event->getRelease();
        
    }
}
