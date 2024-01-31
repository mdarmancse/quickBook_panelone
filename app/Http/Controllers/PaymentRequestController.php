<?php

// PaymentRequestController.php
namespace App\Http\Controllers;

use App\Customer;
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
        // Validate the incoming request data
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Your logic to store the payment-requests request in the local database
        // You can use Eloquent relationships to associate the payment-requests request with customer and items

        // Example:
        $paymentRequest = new PaymentRequest([
            'customer_id' => $request->input('customer_id'),
            // Add other fields as needed
        ]);

        $paymentRequest->save();

        // Attach items to the payment-requests request
        foreach ($request->input('items') as $item) {
            $paymentRequest->items()->attach($item['item_id'], ['quantity' => $item['quantity']]);
        }

        // Your logic to sync with QuickBooks goes here
        // Use the QuickBooks API to create a payment-requests request and associate it with the customer and items

        // Redirect back with success message
        return redirect()->route('payment-requests.index')->with('success', 'Payment request created successfully');
    }
}
