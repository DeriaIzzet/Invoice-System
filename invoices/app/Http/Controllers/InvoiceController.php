<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\LineItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::paginate(7);
        return view('invoices.index', compact('invoices'));
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

    public function edit(Invoice $invoice)
     {
        $invoice->load('lineItems');
         return view('invoices.edit', compact('invoice'));
     }


     public function update( Invoice $invoice,Request $request)
     {
         $validatedData = $request->validate([
            'company_name' => 'required',
            'date' => 'required|date',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|numeric|min:0',
            'line_items.*.unit_price' => 'required|numeric|between:0,999999.99',
         ]);
 
         $invoice->update($validatedData);
        
         foreach ($request->line_items as $itemData) {
            if (isset($itemData['id'])) {
                // Update existing line item
                LineItem::find($itemData['id'])->update($itemData);
            } else {
                // Create new line item
                $invoice->lineItems()->create($itemData);
            }
        }
    
        // Redirect or return response
        return redirect()->route('invoices.index');
     }
 
     // Remove the specified invoice from storage.
     public function destroy(Invoice $invoice)
     {
         $invoice->delete();
         return redirect()->route('invoices.index');
     }
}
