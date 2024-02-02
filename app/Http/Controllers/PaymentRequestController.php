<?php

// PaymentRequestController.php
namespace App\Http\Controllers;

use App\Customer;
use App\Invoice;
use App\InvoiceDetail;

use App\Mail\InvoiceCreated;
use App\Mail\InvoiceUpdated;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


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
        try {

            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            $customer_id = $request->input('customer_id');
            $customerEmail = Customer::where('id', $customer_id)->value('email');

            // Create the invoice
            $invoice = Invoice::create([
                'customer_id' => $request->input('customer_id'),
                'total_before_discount' => $request->input('total_before_discount'),
                'total_after_discount' => $request->input('total_after_discount'),
            ]);


            foreach ($request->input('items') as $item) {
                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['row_total'],
                ]);
            }

            if ($customerEmail) {

                Mail::to($customerEmail)->send(new InvoiceCreated($invoice));


                Log::info('Invoice email sent successfully', ['customer_email' => $customerEmail, 'invoice_id' => $invoice->id]);
            }
        } catch (\Exception $e) {

            Log::error('Failed to create payment request or send invoice email', ['error' => $e->getMessage()]);


            return redirect()->route('payment-requests.index')->with('error', 'Failed to create payment request or send invoice email');
        }


        return redirect()->route('payment-requests.index')->with('success', 'Payment Request created successfully');
    }
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);

        return view('emails.invoice_created',$invoice);
    }

    public function invoiceList()
    {
        $invoices = Invoice::all();
        $data = [];
        $data['menu'] = "payments";
        $data['menu_sub'] = "";
        $data['invoices'] = $invoices;

        return view('payment-requests.invoice-list',$data);
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);

        $customers = Customer::all();


        $data = [
            'menu' => 'payments',
            'menu_sub' => '',
            'invoice' => $invoice,
            'customers' => $customers,
        ];

        return view('payment-requests.edit-invoice', $data);
    }

    public function update(Request $request, $id)
    {
      //  dd($request->all());
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $invoice = Invoice::with('details')->find($id);

            if (!$invoice) {
                return redirect()->route('payment-requests.invoice-list')->with('error', 'Invoice not found');
            }

            $invoice->update([
                'customer_id' => $request->input('customer_id'),
                'total_before_discount' => $request->input('total_before_discount'),
                'total_after_discount' => $request->input('total_after_discount'),
            ]);

            // Clear existing details and recreate them
            $invoice->details()->delete();

            foreach ($request->input('items') as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $invoice->details()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->UnitPrice,
                        'total' => $item['quantity'] * $product->UnitPrice,
                    ]);
                }
            }

            $customer_id = $request->input('customer_id');
            $customerEmail = Customer::where('id', $customer_id)->value('email');

            if ($customerEmail) {
                Mail::to($customerEmail)->send(new InvoiceUpdated($invoice));
                Log::info('Invoice email sent successfully', ['customer_email' => $customerEmail, 'invoice_id' => $invoice->id]);
            }

            DB::commit();

            return redirect()->route('payment-requests.invoice-list')->with('success', 'Invoice updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();

            Log::error('Unexpected error during invoice update', ['error' => $exception->getMessage()]);

            return redirect()->route('payment-requests.edit-invoice', $id)->with('error', $exception->getMessage());
        }
    }



}
