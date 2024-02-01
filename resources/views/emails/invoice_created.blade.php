<p>Dear {{ $invoice->customer->name }},</p>

<p>Your invoice has been successfully created.</p>

<p>Invoice details:</p>
<ul>
    <li>Invoice ID: {{ $invoice->id }}</li>
    <li>Total Before Discount: ${{ $invoice->total_before_discount }}</li>
    <li>Total After Discount: ${{ $invoice->total_after_discount }}</li>

</ul>

<p>Click <a href="{{ route('invoices.show', $invoice->id) }}">here</a> to view your invoice.</p>

<p>Thank you for choosing our services!</p>
