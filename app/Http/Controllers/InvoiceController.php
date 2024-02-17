<?php

// InvoiceController.php
namespace App\Http\Controllers;

use App\Customer;
use App\Invoice;
use App\InvoiceDetail;

use App\Mail\InvoiceCreated;
use App\Mail\InvoiceUpdated;
use App\Product;
use App\QBDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use QuickBooksOnline\API\Facades\Invoice as QBOInvoice;

class InvoiceController extends Controller
{
    // Display the form to create a invoice request
    public function index()
    {
        $customers = Customer::all();

        $products = Product::all();
        $data = [];
        $data['menu'] = "payments";
        $data['menu_sub'] = "";
        $data['customers'] = $customers;
        $data['products'] = $products;
        return view('invoice.index',$data);

    }



public function store(Request $request)
{

    $user = Auth::user();
    $user_id = $user->id;
    $setting = $user->setting;
    $realmId = $setting['QBORealmID'];

    try {
        $dataService = QBDataService::init();


        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $customer_id = $request->input('customer_id');
        $customerData = Customer::where('id', $customer_id)->first();
        $customerEmail =$customerData->email;
        $QBcustomerID =$customerData->quickbooks_id;

        $invoice_last_id=Invoice::latest()->first()->id;







        $lineItems = [];
        foreach ($request->input('items') as $item) {
            $lineItems[] = [
                "Amount" => $item['row_total'],
                "DetailType" => "SalesItemLineDetail",
                "SalesItemLineDetail" => [
                    "ItemRef" => [
                        "value" => $item['product_id']
                        //"value" => 2
                    ],
                    "Qty" => $item['quantity'],
                    "UnitPrice" => $item['unit_price'],
                ]
            ];
        }

        $invoice_no='INV-'.($invoice_last_id+1);


        $theResourceInvoiceObj = QBOInvoice::create([
            "DocNumber"=>$invoice_no,
            "Line" => $lineItems,
            "CustomerRef" => [
                "value" => $QBcustomerID
            ],
            "SalesTermRef" => [
                "value" => 1
            ],
            "DueDate" => $request->input('due_date'),
            "CurrencyRef" => [
                "value" => 'USD',
                "name" => 'US Dollar'
            ],

            "BillEmail" => [
                "Address" => $customerEmail
            ],
            "BillEmailCc" => [
                "Address" => ""
            ],
            "BillEmailBcc" => [
                "Address" => ""
            ]

        ]);
       // echo '<pre>';print_r($theResourceInvoiceObj);exit();

        $resultingInvoiceObj = $dataService->Add($theResourceInvoiceObj);
//        if (!$resultingInvoiceObj) {
//            $error = $dataService->getLastError();
//            echo "Error: " . $error->getResponseBody();
//            exit();
//        }


        if ($resultingInvoiceObj){
            $invoice = Invoice::create([
                'invoice_no' =>$resultingInvoiceObj->Id,
                'customer_id' => $request->input('customer_id'),
                'terms' => $request->input('terms'),
                'invoice_date' => $request->input('invoice_date') ?? date('Y-m-d'),
                'due_date' => $request->input('due_date'),
                'billing_address' => $request->input('billing_address'),
                'total_before_discount' => $request->input('total_before_discount'),
                'total_after_discount' => $request->input('total_after_discount'),
                'discount_percentage' => $request->input('discount_percentage'),
                'realm_id' => $realmId,
                'SyncToken' => 1,
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
        }


    } catch (\Exception $e) {
        Log::error('Failed to create invoice or send invoice email', ['error' => $e->getMessage()]);
        return redirect()->route('invoice.index')->with('error', 'Failed to create invoice or send invoice email');
    }

    return redirect()->route('invoice.index')->with('success', 'Invoice created successfully');
}





    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        $data = [];
        $data['invoice'] = $invoice;

        return view('invoice.show',$data);
    }

    public function invoiceList()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $setting = $user->setting;
        $realmId=$setting['QBORealmID'];
        $invoices = Invoice::where('realm_id', $realmId)->orderBy('invoice_date', 'DESC')->get();
        $data = [];
        $data['menu'] = "payments";
        $data['menu_sub'] = "";
        $data['invoices'] = $invoices;

        return view('invoice.invoice-list',$data);
    }

    public function edit($id)
    {
        $invoice = Invoice::with('details.product')->findOrFail($id);

        $customers = Customer::all();


        $data = [
            'menu' => 'invoices',
            'menu_sub' => '',
            'invoice' => $invoice,
            'customers' => $customers,
        ];

        return view('invoice.edit-invoice', $data);
    }


public function update(Request $request, $id)
{
    $user = Auth::user();
    $user_id = $user->id;
    $setting = $user->setting;
    $realmId = $setting['QBORealmID'];

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
            return redirect()->route('invoice.invoice-list')->with('error', 'Invoice not found');
        }



        $dataService = QBDataService::init();

        $lineItems = [];
        foreach ($invoice->details as $detail) {
            $lineItems[] = [
                "Amount" => $detail->total,
                "DetailType" => "SalesItemLineDetail",
                "SalesItemLineDetail" => [
                    "ItemRef" => [
                        "value" => $detail->product_id // Assuming product_id maps to QBO item id
                    ],
                    "Qty" => $detail->quantity,
                    "UnitPrice" => $detail->unit_price,
                ]
            ];
        }
        $targetInvoiceArray = $dataService->Query("select * from Invoice where id='$invoice->invoice_no'");


        $theInvoice='';
        if(!empty($targetInvoiceArray) && sizeof($targetInvoiceArray) == 1){
            $theInvoice = current($targetInvoiceArray);
        }

        $qbInvoice = QBOInvoice::update($theInvoice, [
            "Line" => $lineItems,
        ]);

         $updatedResult = $dataService->Update($qbInvoice);
        //echo '<pre>';print_r($updatedResult);exit();
        if ($updatedResult){

            $invoice->update([
                'customer_id' => $request->input('customer_id'),
                'terms' => $request->input('terms'),
                'due_date' => $request->input('due_date'),
                'billing_address' => $request->input('billing_address'),
                'total_before_discount' => $request->input('total_before_discount'),
                'total_after_discount' => $request->input('total_after_discount'),
                'discount_percentage' => $request->input('discount_percentage'),
                'realm_id' => $realmId,
                'SyncToken' => $updatedResult->SyncToken,
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

        }else{
            throw new \Exception("Failed to update invoice in QuickBooks Online");

        }

        // Email notification to customer
        $customer_id = $request->input('customer_id');
        $customerEmail = Customer::where('id', $customer_id)->value('email');
        if ($customerEmail) {
            Mail::to($customerEmail)->send(new InvoiceUpdated($invoice));
            Log::info('Invoice email sent successfully', ['customer_email' => $customerEmail, 'invoice_id' => $invoice->id]);
        }

        DB::commit();

        return redirect()->route('invoice.invoice-list')->with('success', 'Invoice updated successfully');
    } catch (\Exception $exception) {
        DB::rollBack();

        Log::error('Unexpected error during invoice update', ['error' => $exception->getMessage()]);

        return redirect()->route('invoice.edit-invoice', $id)->with('error', $exception->getMessage());
    }
}


}
