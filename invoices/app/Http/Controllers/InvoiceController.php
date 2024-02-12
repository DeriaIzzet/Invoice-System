<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;


use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\LineItem;
use Illuminate\Http\Request;
use Exception;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        $searchQuery = $request->input('search');

        $invoices = Invoice::where('user_id', $userId)
            ->when($searchQuery, function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    // Search by invoice ID (integer comparison)
                    $query->where('id', $searchQuery);
                } else {
                    // Search by customer name (string comparison)
                    $query->where('customer_name', 'LIKE', "%{$searchQuery}%");
                }
            })
            ->paginate(7);

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

        $userId = Auth::user()->id;
        // Create the invoice with the total amount
        $invoiceData = $request->all();
        $invoiceData['total_amount'] = $totalAmount;
        $invoiceData['user_id'] = $userId;

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
        try {
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
                Log::info('Processing Line Item:', $itemData);
                
                // if (in_array($itemData['id'] ?? '', $deletedLineItemIds)) {
                //     continue;
                // }

                // Validate line item data
                $validatedLineItem = $this->validateLineItemData($itemData);

                if ($validatedLineItem) {
                    Log::info('Is Valid');
                    if (isset($itemData['id'])) {
                        Log::info('Updating...', $validatedLineItem);
                        // Update existing line item
                        $lineItem = LineItem::find($itemData['id']);
                        $lineItem->update($validatedLineItem);
                    } else {
                        // Create new line item and associate with the invoice
                        Log::info('Creating new line item:', $validatedLineItem);
                        $lineItem = $invoice->lineItems()->create($validatedLineItem);
                    }
                } else {
                    Log::info('INVALID');
                }
                
                // Add to total amount
                $totalAmount += $lineItem->quantity * $lineItem->unit_price;
            }

            // Delete line items marked for deletion
            LineItem::destroy($deletedLineItemIds);

            // Update the total amount of the invoice
            $invoice->total_amount = $totalAmount;
            $invoice->save();
        } catch (Exception $e) {
            // Log the exception
            Log::error('Update failed: ' . $e->getMessage());
            // Optionally re-throw the exception if you want the transaction to fail
            throw $e;
        }
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


public function show(Invoice $invoice)
    {
        // Load related user and line items
        $invoice->load('user', 'lineItems');

        // Return a view and pass the invoice to it
        return view('invoices.show', compact('invoice'));
    }
}
