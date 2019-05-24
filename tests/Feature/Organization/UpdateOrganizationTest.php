<?php

namespace Tests\Feature\Organization;

use Carbon\Carbon;
use Tests\TestCase;
use App\{Organization, User};
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function no_subcribed_user_cannot_update_organization()
    {
        $user = factory(User::class)->create();
        $organization = factory(Organization::class)->create();

        $response = $this->actingAs($user)->put(route('organizations.update', $organization), [
            'name' => 'organization test',
            'email' => 'organization_test@example.com'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'organization test',
            'slug' => 'organization-test',
            'email' => 'organization_test@example.com',
        ]);
    }

    /** @test */
    function guest_cannot_update_organizations()
    {
        $organization = factory(Organization::class)->create();

        $response = $this->put(route('organizations.update', $organization), [
            'name' => 'organization_test',
        ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $response->assertRedirect('login');

        $this->assertDatabaseMissing('organizations', [
            'name' => 'organization_test',
            'slug' => 'organization_test',
        ]);
    }

    /** @test */
    function subcribed_users_cannot_update_organizations_is_not_own()
    {
        $subscription = factory(Subscription::class)->states('active')->create();
        $organization = factory(Organization::class)->create();

        $response = $this->actingAs($subscription->user)->put(route('organizations.update', $organization), [
            'name' => 'organization test',
            'email' => 'organization_test@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'organization test',
            'email' => 'organization_test@example.com',
        ]);
    }

    /** @test */
    function subcribed_users_can_update_organizations_is_own()
    {
        $subscription = factory(Subscription::class)->states('active')->create();
        $organization = factory(Organization::class)->create(['user_id' => $subscription->user->id]);

        $response = $this->actingAs($subscription->user)->put(route('organizations.update', $organization), [
            'name' => 'organization test',
            'email' => 'organization_test@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $response->assertSessionHas(['success' => 'Organización actualizada correctamente.']);

        $this->assertDatabaseHas('organizations', [
            'name' => 'organization test',
            'email' => 'organization_test@example.com',
        ]);
    }

    /** @test */
    function users_subcribed_cancel_cannot_update_organization_it_own()
    {
        $subscription = factory(Subscription::class)->states('ends')->create();
        $organization = factory(Organization::class)->create(['user_id' => $subscription->user->id]);

        Carbon::setTestNow(now()->addDay(6));

        $response = $this->actingAs($subscription->user)->put(route('organizations.update', $organization), [
            'name' => 'organization test',
            'email' => 'organization_test@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'organization test',
            'slug' => 'organization-test',
            'email' => 'organization_test@example.com',
        ]);
    }
}
