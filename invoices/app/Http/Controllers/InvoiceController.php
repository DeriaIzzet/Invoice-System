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

    public function update(Invoice $invoice, Request $request)
{
    // Start database transaction
    DB::transaction(function () use ($invoice, $request) {
        // Validate the main invoice data
        $validatedInvoiceData = $request->validate([
            'company_name' => 'required',
            'date' => 'required|date',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            // Include other invoice fields as necessary
        ]);

        // Update the invoice with validated data
        $invoice->update($validatedInvoiceData);

        // Collect IDs of line items marked for deletion
        $deletedLineItemIds = explode(',', $request->input('deleted_line_items', ''));

        // Initialize total amount
        $totalAmount = 0;

        // Process each line item in the request
        foreach ($request->line_items as $itemData) {
            // Check if line item is marked for deletion
            if (in_array($itemData['id'] ?? '', $deletedLineItemIds)) {
                continue;
            }

            // Validate line item data
            $validatedLineItem = $this->validateLineItemData($itemData);

            if (isset($itemData['id'])) {
                // Update existing line item
                $lineItem = LineItem::find($itemData['id']);
                $lineItem->update($validatedLineItem);
            } else {
                // Create new line item and associate with the invoice
                $lineItem = $invoice->lineItems()->create($validatedLineItem);
            }

            // Add to total amount
            $totalAmount += $lineItem->quantity * $lineItem->unit_price;
        }

        // Delete line items marked for deletion
        LineItem::destroy($deletedLineItemIds);

        // Update the total amount of the invoice
        $invoice->total_amount = $totalAmount;
        $invoice->save();
    });

    // Redirect or return a response after successful update
    return redirect()->route('invoices.index');
}

    private function validateLineItemData(array $data)
    {
        $validator = Validator::make($data, [
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|between:0,999999.99',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $validator->validated();
    }



    // Remove the specified invoice from storage.
    public function destroy($id)
{
    $invoice = Invoice::findOrFail($id);
    $invoice->delete();

    // Redirect to a certain page or return a response
    return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
}
}
