<?php

// PaymentRequestController.php
namespace App\Http\Controllers;

use App\Customer;
use App\Invoice;
use App\InvoiceDetail;

use App\PaymentRequest;
use App\Product;
use Illuminate\Http\Request;


class PaymentRequestController extends Controller
{
    // Display the form to create a payment-requests request
    public function index()
    {
        $customers = Customer::all();

        $products = Product::all();
        $data = [];
        $data['menu'] = "payments";
        $data['menu_sub'] = "";
        $data['customers'] = $customers;
        $data['products'] = $products;
        return view('payment-requests.index',$data);

    }

    // Store the payment-requests request
    public function store(Request $request)
    {
       // dd($request->all());
        // Validate the incoming request data
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
           'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Create the invoice
        $invoice = Invoice::create([
            'customer_id' => $request->input('customer_id'),
            'total_before_discount' => $request->input('total_before_discount'),
            'total_after_discount' => $request->input('total_after_discount'),
        ]);

        // Create invoice details
        foreach ($request->input('items') as $item) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['row_total'],
            ]);
        }

        // Redirect or return a response as needed
        return redirect()->route('payment-requests.index')->with('success', 'Payment Request successfully');
    }
}
