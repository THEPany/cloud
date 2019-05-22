<?php

namespace App\Http\Controllers\Invoice;

use App\Model\Invoice\Invoice;
use App\Organization;
use App\Model\Invoice\Bill;
use App\Model\Invoice\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function create($slug)
    {
        return Inertia::render('Invoice/Bill/Create', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'clients' => $organization->clients->map->only('id', 'name', 'last_name'),
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
