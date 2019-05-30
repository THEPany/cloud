<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\{Mail\OrganizationInvitationEmail, Organization, User};

class InviteContributorToOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function invite_user_to_organization()
    {
        Mail::fake();

        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();

        $organization = factory(Organization::class)->create();
        $organization->addContributor($userA);

        Mail::to($userB)->send(new OrganizationInvitationEmail($organization, $userB));
        Mail::assertQueued(OrganizationInvitationEmail::class, 1);
    }
}
