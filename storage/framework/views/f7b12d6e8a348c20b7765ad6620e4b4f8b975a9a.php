<?php $__env->startSection('content'); ?>
    <!-- Add Bootstrap styles for better design -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" />

    <style>
        /* Additional CSS for styling enhancements */
        .card {
            width: 100%;
            margin: 20px auto;
        }
        .cardTotal {
            width: 80%;
            margin: 20px auto;
        }
        .card-body {
            padding: 20px;
        }

        /* Add more styling as needed */
        .form-control {
            margin-bottom: 10px;
        }

        .btn-info {
            width: 100%;
        }
    </style>

    <section class="content">
        <div class="container">
            <div class="row ">
                <div class="col-lg-12">
                    <div class="card card-info">
                        <div class="card-header text-center">
                            <h3 class="card-title">Create Payment Request</h3>
                        </div>

                        <form class="card-body" style="margin: 10px" action="<?php echo e(route('payment-requests.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <!-- Customer Selection -->
                            <div class="form-group row">
                                <label for="customer_id" class="col-sm-3 col-form-label">Select Customer</label>
                                <div class="col-sm-9">
                                    <?php if(isset($customers) && count($customers) > 0): ?>
                                        <select class="form-control" name="customer_id" id="customer_id" required="">
                                            <option value="">-- Select Customer --</option>
                                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?> (<?php echo e($customer->email); ?>)</option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php else: ?>
                                        <p>No customers available. Please add customers first.</p>
                                    <?php endif; ?>
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

                            <!-- Discount Field -->
                            <div class="form-group row">
                                <label for="discount_percentage" class="col-sm-3 col-form-label">Discount Percentage</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" placeholder="Enter discount percentage" min="0" max="100" oninput="calculateTotal()">
                                </div>
                            </div>

                            <!-- Total Card -->
                            <div class="card cardTotal">
                                <div class="card-header">
                                    <h5 class="card-title">Total</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="total_before_discount">Total Before Discount:</label>
                                            <input type="text" class="form-control" id="total_before_discount"  name="total_before_discount" readonly>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="total_after_discount">Total After Discount:</label>
                                            <input type="text" class="form-control" id="total_after_discount" name="total_after_discount" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Commented out Tax Section -->
                            <!--
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Tax</h5>
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
                            -->

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

    <!-- Your existing script and stylesheet links -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

    <script type="text/javascript">
        // Function to calculate total before and after discount
        function calculateTotal() {
            var totalBeforeDiscount = 0;

            $('.product-row').each(function() {
                var quantity = parseInt($(this).find('.quantity').val());
                var unitPrice = parseFloat($(this).find('.row-total').attr('data-unit-price'));

                if(quantity && unitPrice){
                    var rowTotal = quantity * unitPrice;
                    totalBeforeDiscount += rowTotal;

                    $(this).find('.row-total').val(rowTotal.toFixed(2));
                }
            });

            // Calculate total after discount
            var discountPercentage = parseFloat($('#discount_percentage').val()) || 0;
            var discountAmount = (totalBeforeDiscount * discountPercentage) / 100;
            var totalAfterDiscount = totalBeforeDiscount - discountAmount;

            $('#total_before_discount').val(totalBeforeDiscount.toFixed(2));
            $('#total_after_discount').val(totalAfterDiscount.toFixed(2));
        }

        $(document).ready(function() {
            $('#product_name').on('input', function() {
                var product_name = $(this).val();

                // Make an AJAX request to fetch product suggestions based on the query
                $.ajax({
                    url: '<?php echo e(route("products.autocomplete")); ?>', // Replace with your actual route for product autocomplete
                    method: 'GET',
                    data: { product_name: product_name },
                    dataType: 'json',
                    success: function(data) {
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
                        var suggestion = $('<div class="product-suggestion">' + product.Name + ' - ' + product.UnitPrice + '</div>');

                        suggestion.click(function() {
                            addProductToList(product.id, product.Name, product.UnitPrice);
                            $('#product_name').val('');
                            suggestionsDiv.empty();
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
                    '<td>' + unitPrice + '</td>' +
                    '<td hidden><input type="number" class="form-control unit_price" name="items[' + productId + '][unit_price]" value="' + unitPrice + '"></td>' +
                    '<td hidden><input type="number" class="form-control product_id" name="items[' + productId + '][product_id]" value="' + productId + '"></td>' +
                    '<td><input type="number" class="form-control quantity" name="items[' + productId + '][quantity]" placeholder="Quantity" min="1" required="" oninput="calculateTotal()"></td>' +
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
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/payment-requests/index.blade.php ENDPATH**/ ?>