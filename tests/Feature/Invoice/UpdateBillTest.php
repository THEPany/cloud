<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Organization;
use Laravel\Cashier\Subscription;
use App\Model\Invoice\{Bill, Client, Product};
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateBillTest extends TestCase
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
    function subcribed_user_cannot_update_invoice_bills_type_cash()
    {
        $client = factory(Client::class)->create(['organization_id' => $this->organization->id]);

        $response = $this->withoutExceptionHandling()->actingAs($this->subscription->user)
            ->put(route('invoice.bills.update', $this->organization->slug), [
                'client_id' => $client->id,
                'bill_type' => Bill::TYPE_CASH,
                'paid_out' => 400,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
