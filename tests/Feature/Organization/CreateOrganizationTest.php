<?php

namespace Tests\Feature\Organization;

use Carbon\Carbon;
use Tests\TestCase;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Response;
use App\{Organization, Plan, Restriction, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function no_subcribed_user_cannot_create_organization()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('organizations.store'), [
            'name' => 'Organization Test',
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
    function guest_cannot_create_organizations()
    {
        $response = $this->post(route('organizations.store'), [
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
    function subcribed_users_can_created_organizations()
    {
        $subscription = factory(Subscription::class)->states('active')->create();
        $plan = factory(Plan::class)->create(['braintree_plan' => $subscription->braintree_plan]);
        factory(Restriction::class)->create([
            'plan_id' => $plan->id,
            'restriction_key' => Organization::RESTRICTION_ORGANIZATION,
            'restriction_limit' => 1
        ]);

        $response = $this->actingAs($subscription->user)->post(route('organizations.store'), [
            'name' => 'organization_test',
            'email' => 'organization_test@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $response->assertSessionHas(['flash_success' => 'Organización creada correctamente.']);

        $this->assertDatabaseHas('organizations', [
            'name' => 'organization_test',
            'email' => 'organization_test@example.com',
        ]);

        $this->assertCount(1, \DB::table('contributors')->get());
    }

    /** @test */
    function users_subcribed_cancel_cannot_created_organization()
    {
        $subscription = factory(Subscription::class)->states('ends')->create();

        Carbon::setTestNow(now()->addDay(6));

        $response = $this->actingAs($subscription->user)->post(route('organizations.store'), [
            'name' => 'organization_test',
            'email' => 'organization_test@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'organization_test',
            'slug' => 'organization_test',
            'email' => 'organization_test@example.com',
        ]);
    }

    /** @test */
    function name_organization_is_save_to_lower()
    {
        $this->withoutExceptionHandling();

        $subscription = factory(Subscription::class)->states('active')->create();
        $plan = factory(Plan::class)->create(['braintree_plan' => $subscription->braintree_plan]);
        factory(Restriction::class)->create([
            'plan_id' => $plan->id,
            'restriction_key' => Organization::RESTRICTION_ORGANIZATION,
            'restriction_limit' => 1
        ]);

        $response = $this->actingAs($subscription->user)->post(route('organizations.store'), [
            'name' => 'Organization A',
            'email' => 'organization.a@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseHas('organizations', [
            'name' => 'organization a',
            'slug' => 'organization-a',
            'email' => 'organization.a@example.com',
        ]);
    }

    /** @test */
    function name_organization_is_required()
    {
        $this->handleValidationExceptions();

        $subscription = factory(Subscription::class)->states('active')->create();
        $plan = factory(Plan::class)->create(['braintree_plan' => $subscription->braintree_plan]);
        factory(Restriction::class)->create([
            'plan_id' => $plan->id,
            'restriction_key' => Organization::RESTRICTION_ORGANIZATION,
            'restriction_limit' => 1
        ]);

        $response = $this->actingAs($subscription->user)->post(route('organizations.store'), [
            'email' => 'organization.a@example.com',
        ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertCount(0, Organization::all());
    }
}
