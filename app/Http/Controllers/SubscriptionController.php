<?php

namespace App\Http\Controllers;

use App\Plan;
use Inertia\Inertia;
use Braintree_ClientToken;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Subscription/Index', [
            'token' => Braintree_ClientToken::generate(),
            'isSubscribed' => auth()->user()->isSubscribed(),
            'plans' => Plan::orderBy('price')->get()->map->only('id', 'name', 'braintree_plan', 'price', 'trialDuration', 'description'),
            'plan' => Plan::where('braintree_plan', optional(auth()->user()->subscriptions->last())->only('braintree_plan')['braintree_plan'])->first(),
            'card' => ['card_brand' => auth()->user()->card_brand, 'card_last_four' => auth()->user()->card_last_four],
        ]);
    }

    public function store(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);
        $request->user()
            ->newSubscription('main', $plan->braintree_plan)
            ->trialDays($plan->trialDuration)
            ->create($request->payment_method_nonce);

        return redirect()->route('subscriptions.index')->with(['flash_success' => 'Se ha suscrito correctamente al plan seleccionado.']);
    }

    public function update(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);

        $request->user()
            ->subscription('main')
            ->swap($plan->braintree_plan);

        return redirect()->route('subscriptions.index')->with(['flash_success' => 'Plan cambiado correctamente.']);
    }

    public function updateCard(Request $request)
    {
        $request->user()
            ->updateCard($request->payment_method_nonce);

        return redirect()->route('subscriptions.index')->with(['flash_success' => 'Método de pago cambiado correctamente.']);
    }

}
