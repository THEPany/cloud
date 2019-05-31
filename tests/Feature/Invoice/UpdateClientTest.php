<?php

namespace Tests\Feature\Invoice;

use Bouncer;
use Tests\TestCase;
use App\Organization;
use App\Model\Invoice\Client;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateClientTest extends TestCase
{
    use RefreshDatabase;

    private $subscription;

    private $organization;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscription = factory(Subscription::class)->state('active')->create();
        $this->organization = factory(Organization::class)->create([
            'user_id' => $this->subscription->user->id
        ]);
        $this->client = factory(Client::class)->create(['organization_id' => $this->organization->id]);
    }

    /** @test */
    function subcribed_user_can_update_invoice_clients()
    {
       Bouncer::allow($this->subscription->user)->to('update', $this->client);

        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->put(route('invoice.clients.update', [
                'slug' => $this->organization->slug,
                'client' => $this->client
            ]), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Cliente actualizado correctamente.']);

        $this->assertDatabaseHas('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
            'id_card' => '999-9999999-9'
        ]);
    }

    /** @test */
    function other_subcribed_user_can_update_invoice_clients_if_are_invited_to_organization()
    {
        $subscriptionB = factory(Subscription::class)->state('active')->create();
        $this->organization->addContributor($subscriptionB->user);

        Bouncer::allow($subscriptionB->user)->to('update', $this->client);

        $response = $this->withoutExceptionHandling()->actingAs($subscriptionB->user)
            ->put(route('invoice.clients.update', [
                'slug' => $this->organization->slug,
                'client' => $this->client
            ]), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Cliente actualizado correctamente.']);

        $this->assertDatabaseHas('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
            'id_card' => '999-9999999-9'
        ]);
    }

    /** @test */
    function guest_cannot_update_invoice_clients()
    {
        $response = $this->withExceptionHandling()
            ->put(route('invoice.clients.update', [
                'slug' => $this->organization->slug,
                'client' => $this->client
            ]), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
            'id_card' => '999-9999999-9'
        ]);
    }

    /** @test */
    function subcribed_user_cannot_update_invoice_clients_to_other_organization()
    {
        $subscription = factory(Subscription::class)->state('active')->create();

        $response = $this->withoutExceptionHandling()->actingAs($subscription->user)
            ->put(route('invoice.clients.update', [
                'slug' => $this->organization->slug,
                'client' => $this->client
            ]), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('home.index'));

        $this->assertDatabaseMissing('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'Cristian',
            'last_name' => 'Gomez',
            'id_card' => '999-9999999-9'
        ]);
    }

    /** @test */
    function subcribed_user_cannot_create_invoice_clients_if_the_subscription_expired()
    {
        $this->subscription->update([
            'ends_at' => now()
        ]);

        $response = $this->withExceptionHandling()->actingAs($this->subscription->user)
            ->put(route('invoice.clients.update', [
                'slug' => $this->organization->slug,
                'client' => $this->client
            ]), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'Cristian',
            'last_name' => 'Gomez',
            'id_card' => '999-9999999-9'
        ]);
    }
}
