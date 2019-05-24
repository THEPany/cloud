<?php

namespace Tests\Feature\Invoice;

use App\Model\Invoice\Product;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Organization;
use Laravel\Cashier\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    private $subscription;

    private $organization;

    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscription = factory(Subscription::class)->state('active')->create();
        $this->organization = factory(Organization::class)->create([
            'user_id' => $this->subscription->user->id
        ]);
        $this->product = factory(Product::class)->create(['organization_id' => $this->organization->id]);
    }

    /** @test */
    function subcribed_user_can_update_invoice_products()
    {
        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->put(route('invoice.products.update', [
                'slug' => $this->organization->slug,
                'product' => $this->product
            ]), [
                'name' => 'Producto A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Producto actualizado correctamente.']);

        $this->assertDatabaseHas('invoice_products', [
            'organization_id' => $this->organization->id,
            'name' => 'producto a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function other_subcribed_user_can_update_invoice_products_if_are_invited_to_organization()
    {
        $other_subscription = factory(Subscription::class)->state('active')->create();
        $this->organization->addContributor($other_subscription->user);

        $response = $this->withoutExceptionHandling()->actingAs($other_subscription->user)
            ->put(route('invoice.products.update', [
                'slug' => $this->organization->slug,
                'product' => $this->product
            ]), [
                'name' => 'Producto A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Producto actualizado correctamente.']);

        $this->assertDatabaseHas('invoice_products', [
            'organization_id' => $this->organization->id,
            'name' => 'producto a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function guest_cannot_update_invoice_products()
    {
        $response = $this->withExceptionHandling()
            ->put(route('invoice.products.update', [
                'slug' => $this->organization->slug,
                'product' => $this->product
            ]), [
                'name' => 'Producto A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('invoice_products', [
            'organization_id' => $this->organization->id,
            'name' => 'producto a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function subcribed_user_cannot_update_invoice_products_to_other_organization()
    {
        $subscription = factory(Subscription::class)->state('active')->create();

        $response = $this->withoutExceptionHandling()->actingAs($subscription->user)
            ->put(route('invoice.products.update', [
                'slug' => $this->organization->slug,
                'product' => $this->product
            ]), [
                'name' => 'Producto A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('home.index'));

        $this->assertDatabaseMissing('invoice_products', [
            'organization_id' => $this->organization->id,
            'name' => 'producto a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function subcribed_user_cannot_create_invoice_products_if_the_subscription_expired()
    {
        $this->subscription->update([
            'ends_at' => now()
        ]);

        $response = $this->withExceptionHandling()->actingAs($this->subscription->user)
            ->put(route('invoice.products.update', [
                'slug' => $this->organization->slug,
                'product' => $this->product
            ]), [
                'name' => 'Producto A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('invoice_products', [
            'organization_id' => $this->organization->id,
            'name' => 'producto a',
            'cost' => 200,
        ]);
    }
}
