<?php

namespace Tests\Feature\Organization;

use Tests\TestCase;
use App\{Mail\OrganizationInvitationEmail, Organization, User};
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        Mail::assertSent(OrganizationInvitationEmail::class, 1);
    }
}
