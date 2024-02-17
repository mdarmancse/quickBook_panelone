<?php $__env->startSection('content'); ?>
    <head>
        <!-- Use the following src for the script on your form and replace ****version**** with the desired version: src="https://cdn.cardknox.com/ifields/****version****/ifields.min.js" -->
        <script src="https://cdn.cardknox.com/ifields/2.6.2006.0102/ifields.min.js"></script>
    </head>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><?php echo e(__('Payment Form')); ?></div>

                    <div class="card-body">
                        <form id="payment-form" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input id="name" name="xName" type="text" class="form-control" placeholder="Name" autocomplete="cc-name">
                            </div>
                            <div class="form-group">
                                <label for="ach">Checking Account Number</label>
                                <iframe data-ifields-id="ach" data-ifields-placeholder="Checking Account Number" src="https://cdn.cardknox.com/ifields/2.6.2006.0102/ifield.htm"></iframe>
                                <input data-ifields-id="ach-token" name="xACH" type="hidden">
                            </div>
                            <div class="form-group">
                                <label for="card-number">Card Number</label>
                                <iframe data-ifields-id="card-number" data-ifields-placeholder="Card Number" src="https://cdn.cardknox.com/ifields/2.6.2006.0102/ifield.htm"></iframe>
                                <input data-ifields-id="card-number-token" name="xCardNum" type="hidden">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <iframe data-ifields-id="cvv" data-ifields-placeholder="CVV" src="https://cdn.cardknox.com/ifields/2.6.2006.0102/ifield.htm"></iframe>
                                <input data-ifields-id="cvv-token" name="xCVV" type="">
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input id="amount" name="xAmount" type="text" class="form-control" placeholder="Amount">
                            </div>
                            <div class="form-group">
                                <label for="month">Month</label>
                                <input id="month" name="xMonth" type="text" class="form-control" placeholder="Month" autocomplete="cc-exp-month">
                            </div>
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input id="year" name="xYear" type="text" class="form-control" placeholder="Year" autocomplete="cc-exp-year">
                            </div>
                            <div class="form-group">
                                <button type="submit" id="submit-btn" class="btn btn-primary">Submit</button>
                                <button type="button" id="clear-btn" class="btn btn-secondary">Clear</button>
                            </div>
                            <div class="form-group">
                                <label id="transaction-status"></label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.cardknox.com/ifields/2.6.2006.0102/ifields.min.js"></script>
    <script>
        $(document).ready(function() {
            let defaultStyle = {
                border: '1px solid black',
                'font-size': '14px',
                padding: '3px',
                width: '350px',
                height: '30px',
                borderRadius: '5px'
            };
            let validStyle = {
                border: '1px solid green',
                'font-size': '14px',
                padding: '3px',
                width: '350px',
                height: '30px'
            };
            let invalidStyle = {
                border: '1px solid red',
                'font-size': '14px',
                padding: '3px',
                width: '350px',
                height: '30px'
            };
            enableAutoSubmit('payment-form');
            enable3DS('amount', 'month', 'year', true, 20000);
            setIfieldStyle('ach', defaultStyle);
            setIfieldStyle('card-number', defaultStyle);
            setIfieldStyle('cvv', defaultStyle);
            setAccount('ifields_nexdecadedeve8af1394c5ac4c549b842998e', 'iFields_Sample_Form', '1.0');
            enableAutoFormatting();
            addIfieldCallback('input', function(data) {
                if (data.ifieldValueChanged) {
                    setIfieldStyle('card-number', data.cardNumberFormattedLength <= 0 ? defaultStyle : data.cardNumberIsValid ? validStyle : invalidStyle);
                    if (data.lastIfieldChanged === 'cvv'){
                        setIfieldStyle('cvv', data.issuer === 'unknown' || data.cvvLength <= 0 ? defaultStyle : data.cvvIsValid ? validStyle : invalidStyle);
                    } else if (data.lastIfieldChanged === 'card-number') {
                        if (data.issuer === 'unknown' || data.cvvLength <= 0) {
                            setIfieldStyle('cvv', defaultStyle);
                        } else if (data.issuer === 'amex'){
                            setIfieldStyle('cvv', data.cvvLength === 4 ? validStyle : invalidStyle);
                        } else {
                            setIfieldStyle('cvv', data.cvvLength === 3 ? validStyle : invalidStyle);
                        }
                    } else if (data.lastIfieldChanged === 'ach') {
                        setIfieldStyle('ach',  data.achLength === 0 ? defaultStyle : data.achIsValid ? validStyle : invalidStyle);
                    }
                }
            });
            let checkCardLoaded = setInterval(function() {
                clearInterval(checkCardLoaded);
                focusIfield('card-number');
            }, 1000);
            document.getElementById('clear-btn').addEventListener('click', function(e){
                e.preventDefault();
                clearIfield('card-number');
                clearIfield('cvv');
                clearIfield('ach');
            });
            document.getElementById('payment-form').addEventListener('submit', function(e){
                e.preventDefault();
                document.getElementById('transaction-status').innerHTML = 'Processing Transaction...';
                let submitBtn = this;
                submitBtn.disabled = true;
                getTokens(function() {
                        document.getElementById('transaction-status').innerHTML  = '';
                        document.getElementById('ach-token').innerHTML = document.querySelector("[data-ifields-id='ach-token']").value;
                        document.getElementById('card-token').innerHTML = document.querySelector("[data-ifields-id='card-number-token']").value;
                        document.getElementById('cvv-token').innerHTML = document.querySelector("[data-ifields-id='cvv-token']").value;
                        submitBtn.disabled = false;
                    },
                    function() {
                        document.getElementById('transaction-status').innerHTML = '';
                        document.getElementById('ach-token').innerHTML = '';
                        document.getElementById('card-token').innerHTML = '';
                        document.getElementById('cvv-token').innerHTML = '';
                        submitBtn.disabled = false;
                    },
                    30000
                );
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/invoice/show.blade.php ENDPATH**/ ?>