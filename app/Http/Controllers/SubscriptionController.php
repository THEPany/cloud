<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Braintree_ClientToken;
use Illuminate\Http\Request;
use App\{Organization, Plan, Restriction};

class SubscriptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Subscription/Index', [
            'token' => Braintree_ClientToken::generate(),
            'isSubscribed' => auth()->user()->isSubscribed(),
            'isCancel' => optional(optional(auth()->user()->subscription('main'))->ends_at)->format('d/m/Y') ?: '',
            'isTrial' => auth()->user()->subscription('main')->onTrial() ? auth()->user()->subscription('main')->trial_ends_at->format('d/m/Y') : '',
            'endSubscription' => auth()->user()->subscription('main')->ends_at ? auth()->user()->subscription('main')->ends_at->isToday() : '',
            'plans' => Plan::orderBy('price')->get()->map->only('id', 'name', 'braintree_plan', 'price', 'trialDuration', 'description'),
            'plan' => Plan::where('braintree_plan', optional(auth()->user()->subscriptions->last())->only('braintree_plan')['braintree_plan'])->first(),
            'card' => ['card_brand' => auth()->user()->card_brand, 'card_last_four' => auth()->user()->card_last_four],
        ]);
    }

    public function store(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);

        if ($request->user()->isSubscribed()) {
            $request->user()
                ->newSubscription('main', $plan->braintree_plan)
                ->create($request->payment_method_nonce);
        } else {
            $request->user()
                ->newSubscription('main', $plan->braintree_plan)
                ->trialDays($plan->trialDuration)
                ->create($request->payment_method_nonce);
        }

        return redirect()->route('subscriptions.index')->with('success', 'Se ha suscrito correctamente al plan seleccionado.');
    }

    public function update(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);

        abort_unless($this->restrictionOrganization($plan), 403, 'Límite alcanzado, por favor actualice su plan.');

        foreach ($request->user()->organizations as $organization) {
            abort_unless($this->restrictionContributor($plan, $organization), 403, 'Límite alcanzado, por favor actualice su plan.');
        }

        $request->user()
            ->subscription('main')
            ->swap($plan->braintree_plan);

        return redirect()->route('subscriptions.index')->with('success', 'Plan cambiado correctamente.');
    }

    public function updateCard(Request $request)
    {
        $request->user()
            ->updateCard($request->payment_method_nonce);

        return redirect()->route('subscriptions.index')->with('success', 'Método de pago cambiado correctamente.');
    }

    public function cancelSubscription(Request $request)
    {
        $request->user()->subscription('main')->cancel();

        return redirect()->route('subscriptions.index')->with('success', 'Suscripcion cancelada correctamente.');
    }

    public function resumeSubscription(Request $request)
    {
        $request->user()->subscription('main')->resume();

        return redirect()->route('subscriptions.index')->with('success', 'Suscripcion reanudada correctamente.');
    }

    public function cancelNowSubscription(Request $request)
    {
        $request->user()->subscription('main')->cancelNow();

        return redirect()->route('subscriptions.index')->with('success', 'Suscripcion eliminado correctamente.');
    }

    /**
     * ------------------------------
     * Restriction Plan Organization
     * ------------------------------
     */
    protected function restrictionOrganization(Plan $plan)
    {
        try {
            $restriction = Restriction::query()
                ->whereRestrictionKeyAndPlanId(Organization::RESTRICTION_ORGANIZATION, $plan->id)
                ->first();

            return Organization::query()
                    ->whereHas('contributors', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->count() < $restriction->restriction_limit;
        }catch (\Exception $e) {
            return false;
        }
    }

    protected function restrictionContributor(Plan $plan, Organization $organization)
    {
        try {
            $restriction = Restriction::query()
                ->whereRestrictionKeyAndPlanId(Organization::RESTRICTION_CONTRIBUTOR, $plan->id)
                ->first();

            return $organization->contributors()->count() < $restriction->restriction_limit;

        }catch (\Exception $e) {
            return false;
        }

    }

}
