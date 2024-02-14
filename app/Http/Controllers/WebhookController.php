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

        if ($verified == true) {
            try {


                $payLoad_data = json_decode($payLoad, true);

                foreach ($payLoad_data['eventNotifications'] as $event_noti) {
                    $realmId = $event_noti['realmId'];
                    foreach ($event_noti['dataChangeEvent']['entities'] as $entity) {

                        $name = $entity['name'];
                        $id = $entity['id'];
                        if ($name === 'Customer') {
                            $response = $dataService->FindById('Customer', $id);
                            $insertedData= self::updateOrCreateCustomer($response);
                            mail("mdarmancse@gmail.com", "QB Customer data", json_encode($insertedData));
                        }
                        if ($name === 'Item') {
                            $response = $dataService->FindById('Item', $id);
                            $insertedData=self::updateOrCreateItem($response);
                            mail("amd55077@gmail.com", "QB Item data", json_encode($insertedData));
                        }
                    }
                }
            }catch (\Exception $e){
                mail("amd55077@gmail.com", "QB Error ", json_encode($e->getMessage()));

            }
        }



    }
//    public function getWebhook()
//    {
//
//        $dataService = QBDataService::init();
//        $payLoad = file_get_contents("php://input");
//        $verified = WebhooksService::verifyPayload($this->webhook_token, $payLoad, $_SERVER['HTTP_INTUIT_SIGNATURE']);
//        mail("mdarmancse@gmail.com", "Verified", $verified);
//
//        if ($verified == true) {
//            try {
//
//                $payLoad_data = json_decode($payLoad, true);
//                mail("mdarmancse@gmail.com", "Payload Data", $payLoad);
//
//                foreach ($payLoad_data['eventNotifications'] as $event_noti) {
//                    $realmId = $event_noti['realmId'];
//                    foreach ($event_noti['dataChangeEvent']['entities'] as $entity) {
//                        mail("mdarmancse@gmail.com", "In foreach", $payLoad);
//
//                        $name = $entity['name'];
//                        $id = $entity['id'];
//                        mail("mdarmancse@gmail.com", "Name", $name);
//
//                        if ($name == 'Customer') {
//
//                            $response = $dataService->FindById('Customer', $id);
//                            mail("mdarmancse@gmail.com", "Customer Find", $response);
//
//                            $insertedData= self::updateOrCreateCustomer($response);
//                            //mail("amd55077@gmail.com", "QB Customer data", json_encode($insertedData));
//                            mail("mdarmancse@gmail.com", "QB Customer data", $insertedData);
//                       }
////                        if ($name == 'Item') {
////                            $response = $dataService->FindById('Item', $id);
////                            $insertedData=self::updateOrCreateItem($response);
////                            mail("amd55077@gmail.com", "QB Item data", json_encode($insertedData));
////                        }
//                    }
//                }
//            }catch (\Exception $e){
//                mail("mdarmancse@gmail.com", "QB Error ", json_encode($e->getMessage()));
//
//            }
//        }
//
//
//
//    }
    public function updateOrCreateItem($response)
    {
        return Product::updateOrCreate(
            ['ItemId' => $response->Id],
            [
                'Name' => $response->Name,
                'Description' => $response->Description ?? null,
                'Active' => $response->Active ? 1 : 0,
                'FullyQualifiedName' => $response->FullyQualifiedName,
                'Taxable' => $response->Taxable ? 1 : 0,
                'UnitPrice' => $response->UnitPrice ?? null,
                'Type' => $response->Type ?? null,
                'IncomeAccountRef' => isset($response->IncomeAccountRef) ? json_encode($response->IncomeAccountRef) : null,
                'PurchaseCost' => $response->PurchaseCost ?? null,
                'TrackQtyOnHand' => $response->TrackQtyOnHand ? 1 : 0,
                'SyncToken' => $response->SyncToken,
                'createdby' => 1,
                'updatedby' => 1,
            ]
        );
    }

    public function updateOrCreateCustomer($response)
    {
        $data = Customer::updateOrCreate(
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
            ]
        );

        return $data ;
    }

    public function testGetCustomerApi()
    {
        $dataService = QBDataService::init();

        $id = 2;
        $response = $dataService->FindById('Item', $id);

         $data=self::updateOrCreateItem($response);
        //mail("mdarmancse@gmail.com", "Tst Reposne", json_encode($data));

        return $data;
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();

    }


}
