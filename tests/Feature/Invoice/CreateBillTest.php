<?php

namespace Tests\Feature\Invoice;

use Bouncer;
use Tests\TestCase;
use App\Organization;
use App\Model\Person\Client;
use App\Model\Inventory\Article;
use Laravel\Cashier\Subscription;
use App\Model\Invoice\{Bill, Payment};
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateBillTest extends TestCase
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
    function subcribed_user_can_create_invoice_bills_type_cash()
    {
        $client = factory(Client::class)->create(['organization_id' => $this->organization->id]);

        $articles = factory(Article::class)->times(2)->create([
            'organization_id' => $this->organization->id,
            'cost' => 100,
        ]);

        $response = $this->handleValidationExceptions()->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'client_id' => $client->id,
                'paid_out' => 400,
                'bill_type' => Bill::TYPE_CASH,
                'articles' => [
                    [
                        'id' => $articles[0]->id,
                        'quantity' => 1
                    ],
                    [
                        'id' => $articles[1]->id,
                        'quantity' => 3
                    ]
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'client_id' => $client->id,
            'status' => Bill::STATUS_PAID,
            'bill_type' => Bill::TYPE_CASH,
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'paid_out' => 400
        ]);

        $this->assertDatabaseHas('invoice_bill_article', [
            'article_id' => $articles[0]->id,
            'quantity' => 1,
            'cost' => 100,
            'sub_total' => 100
        ]);

        $this->assertDatabaseHas('invoice_bill_article', [
            'article_id' => $articles[1]->id,
            'quantity' => 3,
            'cost' => 100,
            'sub_total' => 300
        ]);
    }


    /** @test */
    function subcribed_user_cannot_create_invoice_bills_type_cash_without_paid()
    {
        $client = factory(Client::class)->create(['organization_id' => $this->organization->id]);

        $articles = factory(Article::class)->times(2)->create([
            'organization_id' => $this->organization->id,
            'cost' => 100,
        ]);

        $response = $this->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'client_id' => $client->id,
                'bill_type' => Bill::TYPE_CASH,
                'articles' => [
                    [
                        'id' => $articles[0]->id,
                        'quantity' => 1
                    ],
                    [
                        'id' => $articles[1]->id,
                        'quantity' => 3
                    ]
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseMissing('invoice_bills', [
            'organization_id' => $this->organization->id,
            'client_id' => $client->id,
            'bill_type' => Bill::TYPE_CASH,
            'status' => Bill::STATUS_PAID,
        ]);
    }

    /** @test */
    function subcribed_user_can_create_invoice_bills_type_cash_without_client()
    {
        $articles = factory(Article::class)->times(2)->create([
            'organization_id' => $this->organization->id,
            'cost' => 100,
        ]);

        $response = $this->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'bill_type' => Bill::TYPE_CASH,
                'paid_out' => 400,
                'articles' => [
                    [
                        'id' => $articles[0]->id,
                        'quantity' => 1
                    ],
                    [
                        'id' => $articles[1]->id,
                        'quantity' => 3
                    ]
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'bill_type' => Bill::TYPE_CASH,
            'status' => Bill::STATUS_PAID,
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'paid_out' => 400
        ]);

        $this->assertDatabaseHas('invoice_bill_article', [
            'article_id' => $articles[0]->id,
            'quantity' => 1,
            'cost' => 100,
            'sub_total' => 100
        ]);

        $this->assertDatabaseHas('invoice_bill_article', [
            'article_id' => $articles[1]->id,
            'quantity' => 3,
            'cost' => 100,
            'sub_total' => 300
        ]);
    }

    /** @test */
    function subcribed_user_can_create_invoice_bills_type_credit()
    {
        $article = factory(Article::class)->create([
            'organization_id' => $this->organization->id,
            'cost' => 500,
        ]);

        $client = factory(Client::class)->create(['organization_id' => $this->organization->id]);

        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'client_id' => $client->id,
                'bill_type' => Bill::TYPE_CREDIT,
                'paid_out' => 500,
                'expired_at' => now()->day(7)->format('d-m-Y'),
                'articles' => [
                    [
                        'id' => $article->id,
                        'quantity' => 2
                    ],
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'bill_type' => Bill::TYPE_CREDIT,
            'status' => Bill::STATUS_CURRENT,
            'expired_at' => now()->day(7)->toDateTimeString()
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'paid_out' => 500
        ]);

        $this->assertDatabaseHas('invoice_bill_article', [
            'article_id' => $article->id,
            'quantity' => 2,
            'cost' => 500,
            'sub_total' => 1000
        ]);
    }

    /** @test */
    function subcribed_user_can_create_invoice_bills_type_credit_without_paid()
    {
        $article = factory(Article::class)->create([
            'organization_id' => $this->organization->id,
            'cost' => 500,
        ]);

        $client = factory(Client::class)->create(['organization_id' => $this->organization->id]);

        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'client_id' => $client->id,
                'bill_type' => Bill::TYPE_CREDIT,
                'expired_at' => now()->day(7)->format('d-m-Y'),
                'articles' => [
                    [
                        'id' => $article->id,
                        'quantity' => 2
                    ],
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'bill_type' => Bill::TYPE_CREDIT,
            'status' => Bill::STATUS_CURRENT,
            'expired_at' => now()->day(7)->toDateTimeString()
        ]);

        $this->assertCount(0, Payment::all());
    }

    /** @test */
    function subcribed_user_cannot_create_invoice_bills_type_credit_without_client()
    {
        $article = factory(Article::class)->create([
            'organization_id' => $this->organization->id,
            'cost' => 500,
        ]);

        $response = $this->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'bill_type' => Bill::TYPE_CREDIT,
                'paid_out' => 500,
                'expired_at' => now()->day(7)->format('d-m-Y'),
                'articles' => [
                    [
                        'id' => $article->id,
                        'quantity' => 2
                    ],
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseMissing('invoice_bills', [
            'organization_id' => $this->organization->id,
            'bill_type' => Bill::TYPE_CREDIT,
            'status' => Bill::STATUS_CURRENT,
            'expired_at' => now()->day(7)->toDateTimeString()
        ]);

        $this->assertDatabaseMissing('invoice_payments', [
            'paid_out' => 500
        ]);

        $this->assertDatabaseMissing('invoice_bill_article', [
            'article_id' => $article->id,
            'quantity' => 2,
            'cost' => 500,
            'sub_total' => 1000
        ]);
    }
}
