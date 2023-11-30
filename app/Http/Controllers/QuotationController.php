<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class QuotationController extends Controller
{
    public function show($id)
    {
        $quotation = Quotation::findOrFail($id);

        $buyer = new Party([
            'name' => $quotation->name,
            'address' => $quotation->address,
            'code' => '#22663214',
            'custom_fields' => [
                'Phone number' => $quotation->phone_number,
                'account_number' => $quotation->customer?->mpesaId,
            ],
        ]);

        $items = [];
        foreach ($quotation->line_items as $quotation) {
            array_push(
                $items,
                (new InvoiceItem)->title($quotation['item'])
                    ->quantity($quotation['quantity'])
                    ->pricePerUnit($quotation['amount'])
            );
        }

        return Invoice::make()
            ->buyer($buyer)
            ->currencySymbol('KES')
            ->addItems($items)
            ->stream();
    }
}
