@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Invoice Details</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Invoice #{{ $invoice->id }}</h5>
            <p class="card-text"><strong>Company Name:</strong> {{ $invoice->company_name }}</p>
            <p class="card-text"><strong>Date:</strong> {{ $invoice->date }}</p>
            <p class="card-text"><strong>Customer:</strong> {{ $invoice->user->name }}</p>
            <p class="card-text"><strong>Total Amount:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Line Items
        </div>
        <ul class="list-group list-group-flush">
            @foreach ($invoice->lineItems as $lineItem)
                <li class="list-group-item">
                    Description: {{ $lineItem->description }} <br>
                    Quantity: {{ $lineItem->quantity }} <br>
                    Unit Price: ${{ number_format($lineItem->unit_price, 2) }}
                </li>
            @endforeach
        </ul>
    </div>

    <a href="{{ route('invoices.index') }}" class="btn btn-secondary mt-4">Back to Invoice List</a>
</div>
@endsection
