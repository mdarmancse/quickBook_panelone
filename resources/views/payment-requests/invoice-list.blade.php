@extends('layouts.master')

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Invoices</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Invoice List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mr-auto">Manage Invoice</h3>

                            <a href="/payment-requests" id="createUrl" class="btn btn-info ">Add Payment Request</a>
                        </div>
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="card-body">
                            <!-- Invoice table goes here -->
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Total Before Discount</th>
                                    <th>Total After Discount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->customer->name }}</td>
                                        <td>{{ $invoice->total_before_discount }}</td>
                                        <td>{{ $invoice->total_after_discount }}</td>
                                        <td>
                                            <a href="{{ route('payment-requests.edit-invoice', $invoice->id) }}" class="btn btn-success"><i class="nav-icon fas fa-edit"></i></a>
                                            {{-- Uncomment the following lines when you have the delete route and functionality --}}
                                            {{-- <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this invoice?')">Delete</button>
                                            </form> --}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
