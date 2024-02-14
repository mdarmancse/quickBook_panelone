<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use App\QBDataService;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\Exception\ServiceException;

use Illuminate\Support\Facades\Mail;
use QuickBooksOnline\API\WebhooksService\WebhooksService;


class WebhookController extends Controller
{
    public function __construct()
    {
//        $this->dataService = QBDataService::init();
       $this->webhook_token = '3ef9b4a3-6f86-444c-90b9-d3d553aefc39';
    }

    public function index()
    {

            $dataService = QBDataService::init();
            $payLoad = file_get_contents("php://input");
            $verified = WebhooksService::verifyPayload($this->webhook_token, $payLoad, $_SERVER['HTTP_INTUIT_SIGNATURE']);
               mail("amd55077@gmail.com", "QB vERIFY data", $verified);

            if ($verified == true) {
                try {
                mail("amd55077@gmail.com", "QB webhook data", $payLoad);

                $payLoad_data = json_decode($payLoad, true);

                foreach ($payLoad_data['eventNotifications'] as $event_noti) {
                    $realmId = $event_noti['realmId'];
                    foreach ($event_noti['dataChangeEvent']['entities'] as $entity) {
                        $name = $entity['name'];
                        $id = $entity['id'];
                        if ($name == 'Customer') {
                            $response = $dataService->FindById('Customer', $id);
                            mail("amd55077@gmail.com", "QB Customer data", $response);
                            self::updateOrCreateCustomer($response);
                        }
                        if ($name == 'Item') {
                            $response = $dataService->FindById('Item', $id);
                            mail("amd55077@gmail.com", "QB Item data", $response);
                            self::updateOrCreateItem($response);
                        }
                    }
                }
                }catch (\Exception $e){
                    mail("amd55077@gmail.com", "QB Error ", json_encode($e->getMessage()));

                }
            }



    }

    public function updateOrCreateItem($response)
    {
        Product::updateOrCreate(
            ['ItemId' => $response->Id],
            [
                'Name' => $response->Name,
                'Description' => $response->Description ?? null,
                'Active' => $response->Active,
                'FullyQualifiedName' => $response->FullyQualifiedName,
                'Taxable' => $response->Taxable ?? null,
                'UnitPrice' => $response->UnitPrice ?? null,
                'Type' => $response->Type ?? null,
                'IncomeAccountRef' => isset($response->IncomeAccountRef) ? json_encode($response->IncomeAccountRef) : null,
                'PurchaseCost' => $response->PurchaseCost ?? null,
                'TrackQtyOnHand' => $response->TrackQtyOnHand ?? null,
                'SyncToken' => $response->SyncToken,
                'createdby' => 1,
                'updatedby' => 1,
            ]
        );
    }
    private function updateOrCreateCustomer($response)
    {
        // Updating or creating the Customer model based on the 'quickbooks_id'
        Customer::updateOrCreate(
            ['quickbooks_id' => $response->Id],
            [
                'name' => $response->DisplayName,
                'email' => $response->PrimaryEmailAddr->Address ?? null,
                'phone' => $response->PrimaryPhone->FreeFormNumber ?? null,
                'address' => $response->BillAddr->Line1 ?? null,
                'city' => $response->BillAddr->City ?? null,
                'country' => $response->BillAddr->Country ?? null,
                'state' => $response->BillAddr->CountrySubDivisionCode ?? null,
                'zip' => $response->BillAddr->PostalCode ?? null,
                'SyncToken' => $response->SyncToken,

//                'SyncToken' => $resultObj->SyncToken,
//                'name' => $resultObj->DisplayName,
//                'email' => $resultObj->PrimaryEmailAddr->Address ?? null,
//                'phone' => $resultObj->PrimaryPhone->FreeFormNumber?? null,
//                'address' => $resultObj->BillAddr->Line1 ?? null,
//                'city' => $resultObj->BillAddr->City?? null,
//                'country' => $resultObj->BillAddr->Country ?? null,
//                'state' => $resultObj->BillAddr->CountrySubDivisionCode ?? null,
//                'zip' => $resultObj->BillAddr->PostalCode ?? null,
            ]
        );
    }

    public function testGetCustomerApi()
    {
        $dataService = QBDataService::init();

        $id = 2;
        $response = $dataService->FindById('Customer', $id);

        echo "<pre>";
        print_r($response);
        echo "</pre>";
        die();

    }


}