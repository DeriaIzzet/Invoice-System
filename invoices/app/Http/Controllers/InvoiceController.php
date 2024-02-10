<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    private function validateLineItemData(array $data)
    {
        // Define your validation rules here
        $rules = [
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|between:0,999999.99',
        ];

        $validator = Validator::make($data, $rules);
        // Validate the data against the rules
        if ($validator->fails()) {
            // You can throw an exception, or handle it as needed
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        // The validate method will throw an exception if validation fails.
        // The validated data will be returned if validation passes.
        return $validator->validated();
    }


    public function update(Invoice $invoice, Request $request)
    {
        DB::transaction(function () use ($invoice, $request) {
        $validatedData = $request->validate([
            'company_name' => 'required',
            'date' => 'required|date',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
           
        ]);

        $invoice->update($validatedData);

    // Collect IDs of line items to be deleted
    $deletedLineItemIds = explode(',', $request->input('deleted_line_items', ''));

    // Process each line item in the request
    foreach ($request->line_items as $index => $itemData) {
        // Skip validation and deletion if the line item is marked for deletion
        if (in_array($itemData['id'] ?? '', $deletedLineItemIds)) {
            continue;
        }

         $validatedLineItem = $this->validateLineItemData($itemData);

        if (isset($itemData['id'])) {
            // Update existing line item
            LineItem::find($itemData['id'])->update($itemData);
        } else {
            // Create new line item
            $invoice->lineItems()->create($itemData);
        }
    }

    // Delete line items that were marked for deletion
    LineItem::destroy($deletedLineItemIds);
});
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
