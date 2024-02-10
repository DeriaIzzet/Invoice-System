<?php

namespace App\Http\Controllers;

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
            // Add other validation rules as necessary
        ]);

         $invoice = Invoice::create($request->all());

        if ($request->has('line_items')) {
            foreach ($request->line_items as $item) {
                $invoice->lineItems()->create($item);
            }
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

}
