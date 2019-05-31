<?php

namespace App\Observers;

use Bouncer;
use App\Organization;

class OrganizationObserver
{
    /**
     * Handle the app "created" event.
     *
     * @param \App\Organization $organization
     * @return void
     */
    public function created(Organization $organization)
    {
        $organization->addContributor($organization->user);
        Bouncer::allow($organization->user)->everything();
    }

    /**
     * Handle the app "updated" event.
     *
     * @param  \App\Organization  $organization
     * @return void
     */
    public function updated(Organization $organization)
    {
        //
    }

    /**
     * Handle the app "deleted" event.
     *
     * @param  \App\Organization  $organization
     * @return void
     */
    public function deleted(Organization $organization)
    {
        //
    }

    /**
     * Handle the app "restored" event.
     *
     * @param  \App\Organization  $organization
     * @return void
     */
    public function restored(Organization $organization)
    {
        //
    }

    /**
     * Handle the app "force deleted" event.
     *
     * @param  \App\Organization  $organization
     * @return void
     */
    public function forceDeleted(Organization $organization)
    {
        //
    }
}
