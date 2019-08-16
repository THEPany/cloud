<?php

namespace Tests\Feature\Invoice;

use Tests\TestCase;
use App\Organization;
use App\Model\Invoice\{Bill};
use App\Model\Inventory\Article;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_create_payment()
    {
        $subscription = factory(Subscription::class)->create();
        $organization = factory(Organization::class)->create(['user_id' => $subscription->user->id]);
        $bill = factory(Bill::class)->create(['organization_id' => $organization->id]);
        $article = factory(Article::class)->create(['cost' => 1000]);

        DB::table('invoice_bill_article')->insert([
            'bill_id' => $bill->id,
            'article_id' => $article->id,
            'cost' => $article->cost,
            'quantity' => 1,
            'sub_total' => $article->cost
        ]);

        $response = $this->withoutExceptionHandling()->actingAs($subscription->user)
            ->post(route('invoice.payments.store', $organization->slug), [
                'bills' => [
                    [
                        'paid_out' => 400,
                        'id' => $bill->id
                    ]
                ]
            ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHas(['success' => 'Pago creado correctamente.']);

        $this->assertDatabaseHas('invoice_payments', [
            'bill_id' => $bill->id,
            'paid_out' => 400
        ]);
    }
}
