<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use App\Model\Invoice\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{

    public function index($slug)
    {
        return Inertia::render('Invoice/Product/Index', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'filters' => Request::all('search', 'trashed'),
            'products' => $organization->products()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->only('id', 'name', 'cost', 'description', 'deleted_at')
        ]);
    }

    public function create($slug)
    {
        return Inertia::render('Invoice/Product/Create', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
        ]);
    }

    public function store($slug)
    {
        $organization = Organization::whereSlug($slug)->firstOrFail();

        $organization->products()->create(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );

        return redirect()->route('invoice.products.index', $slug)
            ->with(['flash_success' => 'Producto creado correctamente.']);
    }

    public function edit($slug, Product $product)
    {
        return Inertia::render('Invoice/Product/Edit', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'cost' => $product->cost,
                'description' => $product->description,
                'deleted_at' => $product->deleted_at,
            ],
        ]);
    }

    public function update($slug, Product $product)
    {
        $product->update(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );
        return redirect()->route('invoice.products.edit', [
            'slug' => $slug,
            'product' => $product
        ])->with(['flash_success' => 'Producto actualizado correctamente.']);
    }
}
