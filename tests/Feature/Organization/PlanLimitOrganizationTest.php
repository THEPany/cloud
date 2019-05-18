<?php

namespace Tests\Feature\Organization;

use App\Plan;
use App\Restriction;
use Tests\TestCase;
use App\Organization;
use Laravel\Cashier\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class PlanLimitOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function subscribed_user_cannot_create_more_organization_thant_plans_allow()
    {
        $subscription = factory(Subscription::class)->create(['braintree_plan' => 'Basic']);
        factory(Organization::class)->create(['user_id' => $subscription->user->id]);

        $plan = factory(Plan::class)->create(['braintree_plan' => 'Basic']);
        factory(Restriction::class)->create([
            'plan_id' => $plan->id,
            'restriction_key' => Organization::RESTRICTION_ORGANIZATION,
            'restriction_limit' => 1
        ]);

        $response = $this->actingAs($subscription->user)->post(route('organizations.store'), [
            'name' => 'Organization Test',
            'email' => 'organization_test@example.com'
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('organizations', [
            'name' => 'organization test',
            'email' => 'organization_test@example.com'
        ]);
    }
}
