@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Invoice</h1>
    <div>
        @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" class="mb-3">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name:</label>
            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name"
                value="{{ $invoice->company_name }}" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date:</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ $invoice->date }}" required>
        </div>
        <div class="mb-3">
            <label for="customer_name" class="form-label">Customer Name:</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name"
                value="{{ $invoice->customer_name }}" required>
        </div>
        <div class="mb-3">
            <label for="customer_email" class="form-label">Customer Email:</label>
            <input type="email" class="form-control" id="customer_email" name="customer_email"
                placeholder="Customer Email" value="{{ $invoice->customer_email }}" required>
        </div>


        <h2 class="my-4">Line Items</h2>
        <div id="line-items" class="mb-3">
            @if($invoice->lineItems)
            @foreach ($invoice->lineItems as $index => $line_item)
            <div class="line-item">
                <!-- Hidden input for the line item ID -->
                <input type="hidden" name="line_items[{{ $index }}][id]" value="{{ $line_item->id }}">

                <input type="text" name="line_items[0][description]" value="{{ $line_item->description }}" required>
                <input type="number" name="line_items[0][quantity]" value="{{ $line_item->quantity }}" required>
                <input type="number" name="line_items[0][unit_price]" value="{{ $line_item->unit_price }}" required>
                @if ($index > 0)
                 <button type="button" onclick="markForDeletion({{ $line_item->id }})">Delete</button>
                @endif
            </div>
            @endforeach
            @else
            {{-- Display an empty form or message for new invoice --}}
            <p>No line items available.</p>
            @endif
        </div>
        <input type="hidden" name="deleted_line_items" id="deleted_line_items" value="">

        <button type="button" class="btn btn-primary btn-sm mb-3" onclick="addLineItem()">Add Line Item</button>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount:</label>
            <input type="number" step="0.01" id="total_amount" readonly value="{{ $invoice->total }}">
        </div>

         <button type="submit" class="btn btn-success">Update Invoice</button>
    </form>
</div>

<script>
    let lineItemIndex = {{ is_null($invoice->line_items)? 0 : count($invoice->line_items) }};

    function addLineItem() {
        const container = document.getElementById('line-items');
        const newItem = document.createElement('div');
        newItem.classList.add('line-item');
        newItem.innerHTML = `
            <input type="text" name="line_items[${lineItemIndex}][description]" placeholder="Description">
            <input type="number" name="line_items[${lineItemIndex}][quantity]" placeholder="Quantity" onchange="updateTotal()">
            <input type="number" step="0.01" name="line_items[${lineItemIndex}][unit_price]" placeholder="Unit Price" onchange="updateTotal()">
            <button type="button" onclick="removeLineItem(this)">Remove</button>
        `;
        container.appendChild(newItem);
        lineItemIndex++;
    }

    function removeLineItem(button) {
        button.parentElement.remove();
        updateTotal();
    }

    function updateTotal() {
        const lineItems = document.querySelectorAll('.line-item');
        let total = 0;
        lineItems.forEach(item => {
            const quantity = item.querySelector('[name^="line_items"][name$="[quantity]"]').value || 0;
            const unitPrice = item.querySelector('[name^="line_items"][name$="[unit_price]"]').value || 0;
            total += parseFloat(quantity) * parseFloat(unitPrice);
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }
</script>
@endsection