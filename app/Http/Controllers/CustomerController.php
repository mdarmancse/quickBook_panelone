<?php

namespace App\Http\Controllers;

use App\Auth;
use App\Product;
use App\QBDataService;
use Illuminate\Http\Request;
use oasis\names\specification\ubl\schema\xsd\CommonAggregateComponents_2\Attachment;
use QuickBooksOnline\API\Data\IPPAttachable;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Exception\IdsException;
use QuickBooksOnline\API\Facades\JournalEntry;
use QuickBooksOnline\API\Facades\Account;
use RealRashid\SweetAlert\Facades\Alert;
use App\Customer;

use Twilio\Rest\Client;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dataService = $this->QuickbooksDataService();
        $companyInfo = $dataService->getCompanyInfo();

        dd($companyInfo);

        $data = [];
        $data['menu'] = "customer";
        $data['menu_sub'] = "";
        $data['all_customer'] = Customer::all();
        return view('journal-entry.index', $data);
    }
    public function create()
    {
        $data = [];
        $data['products'] = auth()->user()->products;
//        $dataService = $this->QuickbooksDataService();
//        $data['chartOfAccountList'] = $dataService->Query("SELECT * FROM Account");
        //ddd($data['chartOfAccountList']);

        $data['menu'] = "customer";
        $data['menu_sub'] = "create";
        return view('journal-entry.create', $data);
    }

    public function store(Request $request)
    {
        $fileName = "";
        if (isset($request->fileToUpload)) {
            $fileName = $request->file('fileToUpload')->getClientOriginalName();
            $imgBits = base64_encode($request->file('fileToUpload'));
            $extension = $request->file('fileToUpload')->extension();
            $mimeType = $request->file('fileToUpload')->getMimeType();
        }


        $dataService = $this->QuickbooksDataService();

        $payment_date = $request->payment_date;
        $TxnDate = date("Y-m-d", strtotime($payment_date));



        $product_id = $request->product_id;
        $price = $request->price;
        $refund = $request->refund;

        $fullArray = array_map(null, $product_id, $price, $refund);


        $lineArray = [];

        foreach ($fullArray as $row) {
            $product = Product::find($row[0]);

            $type = $product->type;

            // check if refund
            if ($row[2] == 'yes') {
                if ($type == 'Debit') {
                    $type = 'Credit';
                } else {
                    $type = 'Debit';
                }
            }


            $lineArray[] = [
                "Description" => $product->description,
                "Amount" => $row[1],
                "DetailType" => "JournalEntryLineDetail",
                "JournalEntryLineDetail" => [
                        "PostingType" => $type,
                        "AccountRef" => [
                            "value" => "{$product->coa}",
                        ]
                    ]

            ];
        }


        // check the journal entry is valid.

        $debitAmount = 0;
        $creditAmount = 0;

        foreach ($lineArray as $line) {
            if ($line['JournalEntryLineDetail']['PostingType'] === 'Debit') {
                $debitAmount = $debitAmount + (float)$line['Amount'];
            } else {
                $creditAmount = $creditAmount + (float)$line['Amount'];
            }
        }

        // check if they are equal
        if ($debitAmount != $creditAmount) {
            // check the difference

            $difference = abs($debitAmount - $creditAmount);
            $type = 'Debit';
            if ($debitAmount > $creditAmount) {
                $type = 'Credit';
            }

            // add new line
            $lineArray[] = [
                "Description" => 'Report Ledger Balance',
                "Amount" => $difference,
                "DetailType" => "JournalEntryLineDetail",
                "JournalEntryLineDetail" => [
                    "PostingType" => $type,
                    "AccountRef" => [
                        "value" => "68",
                    ]
                ]

            ];
        }



        $dataService->throwExceptionOnError(true);
        try {
            $theResourceObj = JournalEntry::create([
                "TxnDate" => $TxnDate,
                "Line" => $lineArray
            ]);
        } catch (\Exception $e) {
            // send toast with error
            toast($e->getMessage(), 'error');
            return back()->with('error', $e->getMessage());
        }
        try {
            $resultingObj = $dataService->Add($theResourceObj);
        } catch (IdsException $e) {
            toast($e->getMessage(), 'error');
            return back()->with('error', $e->getMessage());
        }
        $error = $dataService->getLastError();
        if ($error) {

            $statusCode =  "The Status code is: " . $error->getHttpStatusCode() . "\n";
            $HelperMessage =  "The Helper message is: " . $error->getOAuthHelperError() . "\n";
            $ResponseMessage = "The Response message is: " . $error->getResponseBody() . "\n";
            $fullMessage = $statusCode.' '.$HelperMessage.' '.$ResponseMessage;
            toast($fullMessage, 'error');
            return back()->with('error', "$fullMessage");
        } else {

            // Attached File
            if (isset($request->fileToUpload)) {
                $attachArr = [
                    "EntityRef" => [
                        "type" => "journalentry",
                        "value" => "$resultingObj->Id"
                    ]
                ];

                $attachmentObj = new IPPAttachable($attachArr);
                $attachableResponse = $dataService->Upload($imgBits, $fileName, $mimeType, $attachmentObj);
            }


            $redirectMessage =  "Created Successfully. Created Id= {$resultingObj->Id}. \n\n";
            //echo "Created Id={$resultingObj->Id}. Reconstructed response body:\n\n";
            //$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingObj, $urlResource);
            //echo $xmlBody . "\n";
            //dd($attachableResponse);
            toast($redirectMessage, 'success');
            return back()->with('success', "$redirectMessage");
        }
    }


    private function QuickbooksDataService()
    {
        return QBDataService::init();
    }
}
