@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <form action="{{ route('invoices.index') }}" method="GET" class="form-inline my-2 my-lg-0">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search invoices..."
            value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Search</button>
    </form>
    <div class="row">

        <div class="col-md-12">

            <h2 class="mb-3"> All Invoices</h2>
            @if ($invoices->isEmpty())
            <div class="alert alert-info">No invoices found.</div>
            @else

            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Invoice Number</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Total Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                        <tr>

                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->date }}</td>
                            <td>{{ $invoice->customer_name }}</td>
                            <td>${{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->id) }}"
                                    class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this invoice?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $invoices->links('pagination::bootstrap-4') }}
            </div>

            @endif
        </div>
    </div>
</div>

@endsection