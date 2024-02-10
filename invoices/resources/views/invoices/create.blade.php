{{-- invoices/create.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Create New Invoice</h1>

        <form action="{{ route('invoices.store') }}" method="POST" class="mb-3">
            @csrf
            @method('post')
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name:</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name" required>
            </div>
            <div class="mb-3">
                <label for="customer_email" class="form-label">Customer Email:</label>
                <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="Customer Email" required>
            </div>

            <h2 class="my-4">Line Items</h2>
            <div id="line-items" class="mb-3">
                <div class="line-item mb-2">
                    <input type="text" class="" name="line_items[0][description]" placeholder="Description" required>
                    <input type="number" class="" name="line_items[0][quantity]" placeholder="Quantity" onchange="updateTotal()" required>
                    <input type="number" class="" step="0.01" name="line_items[0][unit_price]" placeholder="Unit Price" onchange="updateTotal()" required>
                    <button type="button" class="" onclick="removeLineItem(this)">Remove</button>
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="addLineItem()">Add Line Item</button>

            <div class="mb-3">
                <label for="total_amount" class="form-label">Total Amount:</label>
                <input type="number" step="0.01" id="total_amount" readonly>
            </div>

            <button type="submit" class="btn btn-success">Create Invoice</button>
        </form>
    </div>

    <script>
        let lineItemIndex = 1;

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
