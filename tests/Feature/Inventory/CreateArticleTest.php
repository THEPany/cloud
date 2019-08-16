<?php

namespace Tests\Feature\Inventory;

use Bouncer;
use Tests\TestCase;
use App\Organization;
use App\Model\Inventory\Article;
use Laravel\Cashier\Subscription;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
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
    function subcribed_user_can_create_inventory_articles()
    {
        Bouncer::scope()->to($this->organization->id);
        Bouncer::allow($this->subscription->user)->to('create', Article::class);

        $response = $this->actingAs($this->subscription->user)
            ->post(route('inventory.articles.store', $this->organization->slug), [
                'name' => 'Articulo A',
                'cost' => 200,
        ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Articulo creado correctamente.']);

        $this->assertDatabaseHas('inventory_articles', [
            'organization_id' => $this->organization->id,
            'name' => 'articulo a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function other_subcribed_user_can_create_inventory_articles_if_are_invited_to_organization()
    {
        $other_subscription = factory(Subscription::class)->state('active')->create();

        Bouncer::scope()->to($this->organization->id);
        Bouncer::allow($other_subscription->user)->to('create', Article::class);

        $this->organization->addContributor($other_subscription->user);

        $response = $this->withoutExceptionHandling()->actingAs($other_subscription->user)
            ->post(route('inventory.articles.store', $this->organization->slug), [
                'name' => 'Articulo A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Articulo creado correctamente.']);

        $this->assertDatabaseHas('inventory_articles', [
            'organization_id' => $this->organization->id,
            'name' => 'articulo a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function guest_cannot_create_inventory_articles()
    {
        $response = $this->withExceptionHandling()
            ->post(route('inventory.articles.store', $this->organization->slug), [
                'name' => 'Articulo A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('inventory_articles', [
            'organization_id' => $this->organization->id,
            'name' => 'articulo a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function subcribed_user_cannot_create_inventory_articles_to_other_organization()
    {
        $subscription = factory(Subscription::class)->state('active')->create();

        $response = $this->withoutExceptionHandling()->actingAs($subscription->user)
            ->post(route('inventory.articles.store', $this->organization->slug), [
                'name' => 'Articulo A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect(route('home.index'));

        $this->assertDatabaseMissing('inventory_articles', [
            'organization_id' => $this->organization->id,
            'name' => 'articulo a',
            'cost' => 200,
        ]);
    }

    /** @test */
    function subcribed_user_cannot_create_inventory_articles_if_the_subscription_expired()
    {
        $this->subscription->update([
            'ends_at' => now()
        ]);

        $response = $this->withExceptionHandling()->actingAs($this->subscription->user)
            ->post(route('inventory.articles.store', $this->organization->slug), [
                'name' => 'Articulo A',
                'cost' => 200,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('inventory_articles', [
            'organization_id' => $this->organization->id,
            'name' => 'articulo a',
            'cost' => 200,
        ]);
    }
}
