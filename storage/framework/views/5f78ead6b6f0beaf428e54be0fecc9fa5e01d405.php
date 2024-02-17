<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Quickbook Panelone</title>
</head>
<body>
<p>Dear <?php echo e($invoice->customer->name); ?>,</p>

<p>Your invoice has been successfully updated.</p>

<p>Invoice details:</p>
<ul>
    <li>Invoice ID: <?php echo e($invoice->id); ?></li>
    <li>Total Before Discount: <?php echo e($invoice->total_before_discount); ?></li>
    <li>Total After Discount: <?php echo e($invoice->total_after_discount); ?></li>

</ul>

<p>Click <a href="<?php echo e(route('invoices.show', $invoice->id)); ?>">here</a> to view your invoice.</p>

<p>Thank you for choosing our services!</p>

</body>
</html>
<?php /**PATH C:\laragon\www\git\quickBook_panelone\resources\views/emails/invoice_updated.blade.php ENDPATH**/ ?>