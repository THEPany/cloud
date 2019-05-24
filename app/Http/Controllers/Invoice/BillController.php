<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;
use App\Model\Invoice\{Invoice, Bill};
use Illuminate\Support\Facades\Request;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $slug
     * @return \Inertia\Response
     */
    public function index($slug)
    {
        return Inertia::render('Invoice/Bill/Index', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'filters' => Request::all('search', 'status', 'bill_type'),
            'bills' => $organization->bills()
                ->with('client:id,name,last_name', 'payments:id,bill_id,paid_out')
                ->orderByDesc('created_at')
                ->filter(Request::only('search', 'status', 'bill_type'))
                ->paginate()
                ->only('id', 'client', 'created_at', 'total', 'discount','payments', 'expired_at', 'status')
                ->transform(function ($item) {
                    return array_merge($item, [
                        'created_at' => $item['created_at']->format('F d, Y'),
                        'status' => ucwords(strtolower($item['status'])),
                        'payments' => ($item['total'] - $item['discount']) - $item['payments']->sum('paid_out'),
                        'expired_at' => optional($item['expired_at'])->format('F d, Y'),
                    ]);
                })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $slug
     * @return \Inertia\Response
     */
    public function create($slug)
    {
        return Inertia::render('Invoice/Bill/Create', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'clients' => $organization->clients->map->only('id', 'name', 'last_name'),
            'products' => $organization->products->map->only('id', 'name', 'description', 'cost'),
            'type_bill' => [Bill::TYPE_CASH, Bill::TYPE_CREDIT, Bill::TYPE_QUOTATION]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $slug
     * @return \Illuminate\Http\Response
     */
    public function store($slug)
    {
        $invoice = new Invoice(Request::instance());

        return $invoice->create(Organization::whereSlug($slug)->firstOrFail());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $slug
     * @param \App\Model\Invoice\Bill $bill
     * @return \Inertia\Response
     */
    public function edit($slug, Bill $bill)
    {
        return Inertia::render('Invoice/Bill/Edit', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
            'bill' => $bill
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
