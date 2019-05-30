<?php

namespace App\Model\Invoice;

use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
    private $articles;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \App\Model\Invoice\Invoice
     */
    public static function make(Request $request)
    {
        return new Invoice($request);
    }

    /**
     * @param \App\Organization $organization
     * @return mixed
     */
    public function create(Organization $organization)
    {
        $this->findArticles();

        return DB::transaction(function () use ($organization) {
            // Creando factura
            $bill = $organization->bills()->create($this->storeValues());

            // Registrando pago
            if ($this->request->paid_out > 0) {
                $bill->payments()->create($this->request->validate([
                    'paid_out' => ['required', 'numeric', 'min:1']
                ]));
            }

            // Registrado compra de articulos
            $this->registerProduct($bill);

            return $bill;
        });
    }

    /**
     * @return void
     */
    private function findArticles()
    {
        $this->articles = collect($this->request->articles)->map(function ($item) {
            $article = Article::findOrFail($item['id']);
            return [
                'id' => $article->id,
                'cost' => $article->cost,
                'quantity' => $item['quantity'],
                'sub_total' => $article->cost * $item['quantity']
            ];
        });
    }

    /**
     * @return array
     */
    private function storeValues()
    {
        return array_filter(array_merge(
            $this->request->validate([
                'client_id' => ['nullable', 'numeric', Rule::requiredIf($this->request->bill_type === Bill::TYPE_CREDIT)],
                'bill_type' => ['required', Rule::in(Bill::ALL_BILL_TYPE)],
                'discount' => ['nullable', 'numeric', 'min:1'],
                'expired_at' => ['nullable', 'date', Rule::requiredIf($this->request->bill_type === Bill::TYPE_CREDIT)]
            ]),
            [
                'status' => $this->storeStatus()
            ]
        ));
    }

    /**
     * @return string
     */
    private function storeStatus()
    {
        if ($this->articles->sum('sub_total') < $this->request->paid_out) {
            throw ValidationException::withMessages([
                'paid_out' => ['El pago supera el importe total de la factura.'],
            ]);
        } elseif ($this->articles->sum('sub_total') == $this->request->paid_out) {
            if ($this->request->bill_type !== Bill::TYPE_CASH) {
                throw ValidationException::withMessages([
                    'bill_type' => ['El tipo de factura elegido debe ser '. Bill::TYPE_CASH. ' cuando la factura se paga por completo.'],
                ]);
            }
            return Bill::STATUS_PAID;
        } else {
            if ($this->request->bill_type != Bill::TYPE_CREDIT) {
                throw ValidationException::withMessages([
                    'bill_type' => ['El tipo de factura elegido debe ser '. Bill::TYPE_CREDIT. ' cuando la factura no se completa pagar.'],
                ]);
            }
            return Bill::STATUS_CURRENT;
        }
    }

    /**
     * @param \App\Model\Invoice\Bill $bill
     */
    private function registerProduct(Bill $bill)
    {
        $this->articles->each(function ($article) use ($bill) {
            $bill->articles()->attach($article['id'], [
                'cost' => $article['cost'],
                'quantity' => $article['quantity'],
                'sub_total' => $article['cost'] * $article['quantity']
            ]);
        });
    }
}