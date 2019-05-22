<?php

namespace Tests\Feature\Invoice;

use App\Model\Invoice\{Bill, Client, Product};
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Organization;
use Laravel\Cashier\Subscription;
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

        $products = factory(Product::class)->times(2)->create([
            'organization_id' => $this->organization->id,
            'cost' => 100,
        ]);

        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'client_id' => $client->id,
                'bill_type' => Bill::TYPE_CASH,
                'paid_out' => 400,
                'products' => [
                    [
                        'product_id' => $products[0]->id,
                        'quantity' => 1
                    ],
                    [
                        'product_id' => $products[1]->id,
                        'quantity' => 3
                    ]
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['flash_success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'client_id' => $client->id,
            'bill_type' => Bill::TYPE_CASH,
            'status' => Bill::STATUS_PAID,
            'total' => 400
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'paid_out' => 400
        ]);

        $this->assertDatabaseHas('invoice_bill_product', [
            'product_id' => $products[0]->id,
            'quantity' => 1,
            'cost' => 100,
            'sub_total' => 100
        ]);

        $this->assertDatabaseHas('invoice_bill_product', [
            'product_id' => $products[1]->id,
            'quantity' => 3,
            'cost' => 100,
            'sub_total' => 300
        ]);
    }

    /** @test */
    function subcribed_user_can_create_invoice_bills_type_cash_without_client()
    {
        $products = factory(Product::class)->times(2)->create([
            'organization_id' => $this->organization->id,
            'cost' => 100,
        ]);

        $response = $this->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'bill_type' => Bill::TYPE_CASH,
                'paid_out' => 400,
                'products' => [
                    [
                        'product_id' => $products[0]->id,
                        'quantity' => 1
                    ],
                    [
                        'product_id' => $products[1]->id,
                        'quantity' => 3
                    ]
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['flash_success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'bill_type' => Bill::TYPE_CASH,
            'status' => Bill::STATUS_PAID,
            'total' => 400
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'paid_out' => 400
        ]);

        $this->assertDatabaseHas('invoice_bill_product', [
            'product_id' => $products[0]->id,
            'quantity' => 1,
            'cost' => 100,
            'sub_total' => 100
        ]);

        $this->assertDatabaseHas('invoice_bill_product', [
            'product_id' => $products[1]->id,
            'quantity' => 3,
            'cost' => 100,
            'sub_total' => 300
        ]);
    }

    /** @test */
    function subcribed_user_can_create_invoice_bills_type_credit()
    {
        $product = factory(Product::class)->create([
            'organization_id' => $this->organization->id,
            'cost' => 500,
        ]);

        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->post(route('invoice.bills.store', $this->organization->slug), [
                'bill_type' => Bill::TYPE_CREDIT,
                'paid_out' => 500,
                'expired_at' => now()->day(7),
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2
                    ],
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['flash_success' => 'Factura creada correctamente.']);

        $this->assertDatabaseHas('invoice_bills', [
            'organization_id' => $this->organization->id,
            'bill_type' => Bill::TYPE_CREDIT,
            'status' => Bill::STATUS_CURRENT,
            'total' => 1000,
            'expired_at' => now()->day(7)->toDateTimeString()
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'paid_out' => 500
        ]);

        $this->assertDatabaseHas('invoice_bill_product', [
            'product_id' => $product->id,
            'quantity' => 2,
            'cost' => 500,
            'sub_total' => 1000
        ]);
    }
}
