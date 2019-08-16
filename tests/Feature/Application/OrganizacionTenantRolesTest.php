<?php

namespace Tests\Feature\Application;

use Bouncer;
use Tests\TestCase;
use App\Organization;
use App\Model\Person\Client;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizacionTenantRolesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_user_can_create_a_client_in_one_organization_and_in_another_not_because_he_does_not_have_permissions()
    {
        $subscriptionA = factory(Subscription::class)->state('active')->create();
        $organizationA = factory(Organization::class)->create([
            'user_id' => $subscriptionA->user->id
        ]);

        // Usuario X intenta crear un cliente en organizacion A
        Bouncer::scope()->to($organizationA->id);

        $subscriptionA = factory(Subscription::class)->state('active')->create();

        Bouncer::allow($subscriptionA->user)->to('create', Client::class);

        $organizationA->addContributor($subscriptionA->user);

        $this->withoutExceptionHandling()->actingAs($subscriptionA->user)
            ->post(route('person.clients.store', $organizationA->slug), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ])->assertStatus(Response::HTTP_FOUND);

        // Usuario X intenta crear un cliente en organizacion B
        $subscriptionB = factory(Subscription::class)->state('active')->create();
        $organizationB = factory(Organization::class)->create(['user_id' => $subscriptionB->user->id]);
        $organizationB->addContributor($subscriptionA->user);

        Bouncer::scope()->to($organizationB->id);

        $this->withExceptionHandling()->actingAs($subscriptionA->user)
            ->post(route('person.clients.store', $organizationB->slug), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ])->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
