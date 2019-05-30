<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\{Organization, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddOrRemoveContributorToOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_add_other_users_to_owns_organization()
    {
        $organization_test = factory(Organization::class)->create([
            'name' => 'organization_test'
        ]);

        $usersContributors = factory(User::class)->times(4)->create();
        $usersNotContributors = factory(User::class)->times(4)->create();

        foreach ($usersContributors as $user) {
            $organization_test->addContributor($user);
        }

        foreach ($usersContributors as $user) {
            $this->assertDatabaseHas('contributors', [
               'organization_id' => $organization_test->id,
               'user_id' => $user->id
            ]);
        }

        foreach ($usersNotContributors as $user) {
            $this->assertDatabaseMissing('contributors', [
                'organization_id' => $organization_test->id,
                'user_id' => $user->id
            ]);
        }

        $this->assertCount(5, DB::table('contributors')->get());
    }
}
