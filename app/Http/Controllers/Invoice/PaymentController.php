<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use App\Model\Invoice\Payment;
use App\Http\Controllers\Controller;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\{DB, Redirect, Request, Validator};

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Organization $organization
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Organization $organization)
    {
        $this->authorize('view', Payment::class);

        return Inertia::render('Invoice/Payment/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organization' => $organization,
            'payments' => $organization->payments()->with('bill')->paginate()
                ->transform(function ($item) {
                    return collect($item->toArray())->merge([
                        'created_at' => $item->created_at->format('d-m-Y')
                    ]);
                })->only('id', 'paid_out', 'created_at', 'bill')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Organization $organization
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Organization $organization)
    {
        $this->authorize('create', Payment::class);

        return Inertia::render('Invoice/Payment/Create', [
            'organization' => $organization,
            'clients' => $organization->clients()->with('bills')->get()->map(function ($item) {
                return collect($item->toArray())->merge([
                    'pending' => $item->all_due_amount,
                    'bills' => $item->bills->map(function ($item) {
                        return collect($item->toArray())->merge([
                           'id' => $item->id,
                           'total' => $item->total(),
                           'pending' => $item->dueAmount()
                        ])->only('id', 'articles', 'total', 'pending');
                    })
                ]);
            })->map->only('id', 'name', 'last_name', 'id_card', 'pending', 'bills')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Organization $organization
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Organization $organization)
    {
        $this->authorize('create', Payment::class);

        DB::transaction(function () use ($organization) {
            foreach (Request::get('bills') as $item) {
                $bill = $organization->bills()->whereId($item['id'])->firstOrFail();

                $bill->payments()->create(
                    Validator::make($item, [
                        'paid_out' => ['required', 'min:1', 'max:'. $bill->dueAmount()]
                    ])->validate()
                );
            }
        });

        return Redirect::route('invoice.payments', $organization)->with('success', 'Pago creado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Organization $organization
     * @param \App\Model\Invoice\Payment $payment
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Organization $organization, Payment $payment)
    {
        $this->authorize('view', $payment);

        return Inertia::render('Invoice/Payment/Show', [
            'organization' =>  $organization,
            'payment' => $payment,
            'bill' => $payment->bill()->with('client')->whereId($payment->bill_id)->firstOrFail()
        ]);
    }

    public function preview(Organization $organization, Payment $payment)
    {
        $this->authorize('view', $payment);

        return PDF::loadView('payments.default', [
            'organization' => $organization,
            'payment' => $payment->load('bill'),
        ])->stream("Recibo-{$payment->id}-{$payment->created_at->format('dmY')}");
    }
}
