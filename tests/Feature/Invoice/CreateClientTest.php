<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Organization;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateClientTest extends TestCase
{
    use RefreshDatabase;

    private $subscription;

    private $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscription = factory(Subscription::class)->state('active')->create();
        $this->organization = factory(Organization::class)->create([
            'user_id' => $this->subscription->user->id
        ]);
    }

    /** @test */
    function subcribed_user_can_create_invoice_clients()
    {
        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->post(route('invoice.clients.store', $this->organization->slug), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['flash_success' => 'Cliente creado correctamente.']);

        $this->assertDatabaseHas('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
            'id_card' => '999-9999999-9'
        ]);
    }

    /** @test */
    function other_subcribed_user_can_create_invoice_client_if_are_invited_to_organization()
    {
        $other_subscription = factory(Subscription::class)->state('active')->create();
        $this->organization->addContributor($other_subscription->user);

        $response = $this->withoutExceptionHandling()->actingAs($other_subscription->user)
            ->post(route('invoice.clients.store', $this->organization->slug), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['flash_success' => 'Cliente creado correctamente.']);

        $this->assertDatabaseHas('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
            'id_card' => '999-9999999-9'
        ]);
    }

    /** @test */
    function guest_cannot_create_invoice_clients()
    {
        $response = $this->withExceptionHandling()
            ->post(route('invoice.clients.store', $this->organization->slug), [
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
    function subcribed_user_cannot_create_invoice_clients_to_other_organization()
    {
        $subscription = factory(Subscription::class)->state('active')->create();

        $response = $this->withoutExceptionHandling()->actingAs($subscription->user)
            ->post(route('invoice.products.store', $this->organization->slug), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('home.index'));

        $this->assertDatabaseMissing('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
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
            ->post(route('invoice.products.store', $this->organization->slug), [
                'name' => 'Cristian',
                'last_name' => 'Gomez',
                'id_card' => '999-9999999-9'
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('invoice_clients', [
            'organization_id' => $this->organization->id,
            'name' => 'cristian',
            'last_name' => 'gomez',
            'id_card' => '999-9999999-9'
        ]);
    }
}
