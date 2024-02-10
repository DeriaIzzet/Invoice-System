<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('invoices.index');
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'date' => 'required|date',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|numeric|min:0',
            'line_items.*.unit_price' => 'required|numeric|between:0,999999.99',
        ]);

        // Initialize total amount
        $totalAmount = 0;

        // Check if line items exist and calculate total amount
        if ($request->has('line_items')) {
            foreach ($request->line_items as $item) {
                $total = $item['quantity'] * $item['unit_price'];
                $totalAmount += $total;
            }
        }

        // Create the invoice with the total amount
        $invoiceData = $request->all();
        $invoiceData['total_amount'] = $totalAmount;

        $invoice = Invoice::create($invoiceData);

        // Create line items
        if ($request->has('line_items')) {
            foreach ($request->line_items as $item) {
                $invoice->lineItems()->create($item);
            }
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }
}
