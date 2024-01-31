@extends('layouts.master')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" />
    <section class="content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-info">
                        <div class="card-header text-center">
                            <h3 class="card-title">Create Payment Request</h3>
                        </div>

                        <form class="card-body" style="margin: 10px" action="{{ route('payment-requests.store') }}" method="POST">
                            @csrf

                            <!-- Customer Selection -->
                            <div class="form-group row">
                                <label for="customer_id" class="col-sm-3 col-form-label">Select Customer</label>
                                <div class="col-sm-9">
                                    @if(isset($customers) && count($customers) > 0)
                                        <select class="form-control" name="customer_id" id="customer_id" required="">
                                            <option value="">-- Select Customer --</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p>No customers available. Please add customers first.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Product Selection -->
                            <div class="form-group row">
                                <label for="product_name" class="col-sm-3 col-form-label">Select Products</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Search for products" autocomplete="off">
                                    <div id="product_suggestions"></div>
                                </div>
                            </div>

                            <!-- Products List -->
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id="products_list"></tbody>
                                </table>
                            </div>

                            <!-- Total Card -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Total</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="total_before_tax">Total Before Tax:</label>
                                            <input type="text" class="form-control" id="total_before_tax" readonly>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="total_after_tax">Total After Tax:</label>
                                            <input type="text" class="form-control" id="total_after_tax" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-info">Create Payment Request</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#product_name').on('input', function() {
                var product_name = $(this).val();

                // Make an AJAX request to fetch product suggestions based on the query
                $.ajax({
                    url: '{{ route("products.autocomplete") }}', // Replace with your actual route for product autocomplete
                    method: 'GET',
                    data: { product_name: product_name },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Received data:', data);
                        displayProductSuggestions(data);
                    }

                });
            });

            // Function to display product suggestions
            function displayProductSuggestions(data) {
                var suggestionsDiv = $('#product_suggestions');
                suggestionsDiv.empty();

                if (data.length > 0) {
                    data.forEach(function(product) {
                        var suggestion = $('<div class="product-suggestion">' + product.Name + ' - $' + product.UnitPrice + '</div>');

                        suggestion.click(function() {
                            addProductToList(product.id, product.Name, product.UnitPrice);
                            $('#product_name').val(''); // Reset the input field
                            suggestionsDiv.empty(); // Clear suggestions
                            calculateTotal();
                        });

                        suggestionsDiv.append(suggestion);
                    });
                } else {
                    suggestionsDiv.append('<div>No matching products found</div>');
                }
            }

            // Function to add product to the list
            function addProductToList(productId, productName, unitPrice) {
                var productRow = '<tr class="product-row" data-product-id="' + productId + '">' +
                    '<td>' + productName + '</td>' +
                    '<td>$' + unitPrice + '</td>' +
                    '<td><input type="number" class="form-control quantity" name="items[' + productId + '][quantity]" placeholder="Quantity" min="1" required="" onkeyup="calculateTotal()"></td>' +
                    '<td><input type="text" class="form-control row-total" name="items[' + productId + '][row_total]" readonly data-unit-price="' + unitPrice + '"></td>' +
                    '<td><button type="button" class="btn btn-danger btn-sm remove-product"><i class="fas fa-trash"></i></button></td>' +
                    '</tr>';
                $('#products_list').append(productRow);
            }

            // Remove product from the list
            $('#products_list').on('click', '.remove-product', function () {
                $(this).closest('tr').remove();
                calculateTotal();
            });

            // Calculate total before and after tax
            function calculateTotal() {
                var totalBeforeTax = 0;

                $('.product-row').each(function() {
                    var quantity = parseInt($(this).find('.quantity').val());
                    var unitPrice = parseInt($(this).find('.row-total').attr('data-unit-price'));


                        var rowTotal = quantity * unitPrice;
                        totalBeforeTax += rowTotal;

                        console.log(quantity) 
                        console.log(unitPrice)
                        console.log(rowTotal)

                        $(this).find('.row-total').val(rowTotal.toFixed(2));

                });

                // Calculate total after tax (add your tax logic here)
                var additionalTax = 0.1; // Change this to your actual tax rate
                var totalAfterTax = totalBeforeTax * (1 + additionalTax);

                $('#total_before_tax').val(totalBeforeTax.toFixed(2));
                $('#total_after_tax').val(totalAfterTax.toFixed(2));
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>
@endsection
