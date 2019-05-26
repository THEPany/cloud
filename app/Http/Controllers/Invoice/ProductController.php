<?php

namespace App\Http\Controllers\Invoice;

use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Organization;
use App\Model\Invoice\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    /**
     * @param $slug
     * @return \Inertia\Response
     */
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
                ->transform(function ($item) {
                    return array_merge($item, ['description' => Str::limit($item['description'], 50, '...')]);
                })
        ]);
    }

    /**
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function create($slug)
    {
        return Inertia::render('Invoice/Product/Create', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
        ]);
    }

    /**
     * @param $slug
     * @return \Illuminate\Http\RedirectResponse
     */
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

        return Redirect::route('invoice.products.index', $slug)
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Product $product
     * @return \Illuminate\Contracts\View\View
     */
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

    /**
     * @param $slug
     * @param \App\Model\Invoice\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($slug, Product $product)
    {
        $product->update(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );

        return Redirect::route('invoice.products.edit', [
            'slug' => $slug,
            'product' => $product
        ])->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Product $product
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($slug, Product $product)
    {
        $product->delete();
        return Redirect::route('invoice.products.edit', [
            'slug' => $slug,
            'product' => $product
        ]);
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($slug, Product $product)
    {
        $product->restore();
        return Redirect::route('invoice.products.edit', [
            'slug' => $slug,
            'product' => $product
        ]);
    }
}
