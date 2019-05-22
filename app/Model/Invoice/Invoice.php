<?php

namespace App\Model\Invoice;

use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Invoice
{
    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $products;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \App\Organization $organization
     * @return mixed
     */
    public function create(Organization $organization)
    {
        $this->findProducts();

        return DB::transaction(function () use ($organization) {
            // Creando factura
            $bill = $organization->bills()->create($this->storeValues());

            // Registrando pago
            $bill->payments()->create(['paid_out' => $this->request->paid_out]);

            // Registrado compra de productos
            $this->registerProduct($bill);

            return Redirect::route('invoice.bills.create', ['slug' => $organization->slug])
                ->with(['flash_success' => 'Factura creada correctamente.']);

        });
    }

    private function findProducts()
    {
        $this->products = collect($this->request->products)->map(function ($item) {
            $product = Product::findOrFail($item['product_id']);
            return [
                'id' => $product->id,
                'cost' => $product->cost,
                'quantity' => $item['quantity'],
                'sub_total' => $product->cost * $item['quantity']
            ];
        });
    }

    private function storeValues()
    {
        return array_merge(
            $this->request->validate([
                'client_id' => ['nullable', 'numeric'],
                'bill_type' => ['required', 'in:'. Bill::TYPE_CASH.','.Bill::TYPE_CREDIT.','.Bill::TYPE_QUOTATION],
                'discount' => ['nullable', 'numeric'],
                'expired_at' => ['date', Rule::requiredIf($this->request->bill_type === Bill::TYPE_CREDIT)]
            ]),
            [
                'status' => $this->storeStatus(),
                'total' => $this->products->sum('sub_total')
            ]
        );
    }

    private function storeStatus()
    {
        if ($this->products->sum('sub_total') < $this->request->paid_out) {
            throw ValidationException::withMessages([
                'paid_out' => ['El pago supera el importe total de la factura.'],
            ]);
        } elseif ($this->products->sum('sub_total') === $this->request->paid_out) {
            if ($this->request->bill_type !== Bill::TYPE_CASH) {
                throw ValidationException::withMessages([
                    'bill_type' => ['El tipo de factura elegido debe ser '. Bill::TYPE_CASH. ' cuando la factura se paga por completo.'],
                ]);
            }
            return Bill::STATUS_PAID;
        } else {
            if ($this->request->bill_type !== Bill::TYPE_CREDIT) {
                throw ValidationException::withMessages([
                    'bill_type' => ['El tipo de factura elegido debe ser '. Bill::TYPE_CREDIT. ' cuando la factura no se completa pagar.'],
                ]);
            }
            return Bill::STATUS_CURRENT;
        }
    }

    private function registerProduct(Bill $bill)
    {
        $this->products->each(function ($product) use ($bill) {
            $bill->products()->attach($product['id'], [
                'cost' => $product['cost'],
                'quantity' => $product['quantity'],
                'sub_total' => $product['cost'] * $product['quantity']
            ]);
        });
    }
}