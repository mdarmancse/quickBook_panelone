<?php

namespace App\Http\Controllers;

use App\Product;
use App\QBDataService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;


class ProductController extends Controller
{

    public function index()
    {

        /*
      $dataService = QBDataService::init();
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
       $data['products'] = auth()->user()->products;
        try {
            $data['qb_products'] = $dataService->Query("SELECT * FROM item");
        } catch (\Exception $e) {
            $data['qb_products'] = [];
        }
        */
        $user_id = Auth::user()->id;

        $data = [];
        $products = Product::where('createdby',$user_id)->orderBy('id','DESC')->get();
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $data['qb_products'] = $products;



        return view('products.index', $data);
    }


    public function create($id= null)
    {
        // form
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
//        $dataService = QBDataService::init();
//        $data['product'] = $dataService->FindById('item', $id);
//        $data['coas'] = $dataService->Query("SELECT * FROM Account");
        return view('products.create', $data);
    }


    public function store(Request $request)
    {
        $user_id = Auth::user()->id;

        try {

            DB::beginTransaction();
            $dataService = QBDataService::init();
            $data = [
                'Name' => $request->input('name'),
                'Description' => $request->input('description'),
                'UnitPrice' => $request->input('unit_price'),
                'IncomeAccountRef' => [
                    'name' => 'Sales of Product Income',
                    'value' => '79',
                ],
                'AssetAccountRef' => [
                    'name' => 'Inventory Asset',
                    'value' => '81',
                ],
                'Type' => $request->input('type'),
                'ExpenseAccountRef' => [
                    'name' => 'Cost of Goods Sold',
                    'value' => '80',
                ],
            ];


//            $data = [
//                "TrackQtyOnHand" => true,
//                "Name" => "ghgjhjh",
//                "QtyOnHand" => 10,
//                "IncomeAccountRef" => [
//                    "name" => "Sales of Product Income",
//                    "value" => "79"
//                ],
//                "AssetAccountRef" => [
//                    "name" => "Inventory Asset",
//                    "value" => "81"
//                ],
//                "InvStartDate" => "2015-01-01",
//                "Type" => "Service",
//                "ExpenseAccountRef" => [
//                    "name" => "Cost of Goods Sold",
//                    "value" => "80"
//                ]
//            ];

            $quickbooksResponse = \QuickBooksOnline\API\Facades\Item::create($data);
            $resultObj = $dataService->Add($quickbooksResponse);

           // echo '<pre>';print_r($resultObj);exit();

//            $error = $dataService->getLastError();
//            if ($error) {
//                echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
//                echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
//                echo "The Response message is: " . $error->getResponseBody() . "\n";
//            }else {
//                echo "Created Id={$resultObj->Id}. Reconstructed response body:\n\n";
//                $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultObj, $urlResource);
//                echo $xmlBody . "\n";
//            }
//            exit();
            if ($resultObj) {

                $productArray = [
                    'ItemId' => $resultObj->Id,
                    'Name' => $resultObj->Name,
                    'Description' => $resultObj->Description ?? null,
                    'Active' => $resultObj->Active,
                    'FullyQualifiedName' => $resultObj->FullyQualifiedName,
                    'Taxable' => $resultObj->Taxable,
                    'UnitPrice' => $resultObj->UnitPrice,
                    'Type' => $resultObj->Type->value,
                    'IncomeAccountRef' => json_encode($resultObj->IncomeAccountRef),
                    'PurchaseCost' => $resultObj->PurchaseCost,
                    'TrackQtyOnHand' => $resultObj->TrackQtyOnHand,
                    'domain' => $resultObj->domain,
                    'sparse' => $resultObj->sparse,
                    'SyncToken' => $resultObj->SyncToken,
                    'createdby' => $user_id,
                    'updatedby' => $user_id,
                ];



                Product::create($productArray);

                DB::commit();

                return redirect()->route('products.index')->with('success', 'Product created successfully');
            } else {

                DB::rollBack();

                return redirect()->route('products.create')->with('error', 'Something went wrong!');
            }
        } catch (QueryException $e) {

            DB::rollBack();
            return redirect()->route('products.create')->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {

            DB::rollBack();
            return redirect()->route('products.create')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    private function createQuickBooksItem(array $data)
    {

        $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..6JpYYJuDlI3cNYNOyIW37g.-3Rf2E9Ks4069_KpUcSFmedzzkHwsAXrHxXkbvEy8YIvhDL6M-D5Cy8MgzX7EDnIZWm8m983A1NvnghT0NoXOCNzcN4fUrXhcnC9FoWA6_-13_h4Yzu_yW0LPem8_pNuwSTZ-a3H2jFq9UFijKnnSiSq3pGFQZ0WiPW2qpgYP0Sb4QDHLHEtjFbC4Py7d33d991myb49_LZFO5Pv9eQuhfFK6NbDDLfup6iFvkiwas_t_AnqZHI1Lsrv4jrEVXelKZcTcqAGFPLUYajDR527_CmZlwiNG4Orgfi2wVQ19kR2NEFogr7gS3aszdzhCch0p-0VfALJZ2vP16VfvkkQtChmnh8SFxuGDe4N-soxDceSXv_GY83gYtZCUXT0KNbr6IYCM5AE8QkIvml6GbReNNRWZ0Sam_l04kOuJg61645k19RrVuWDJ1UsbgQO9iTZ7ORQhv0ZMpFdWZwk5KteU_hhD3c_no4925_s6gIpf5EgNgEwdhBUEk9tyoOVk4oEgIUWRFT0bH2zvlP15or9o9z91V6dkyMLI9qRtDbRMkK85oWIpYlsmSQuDZkY0FD2bNAEnnTBWnseb9Ihh0wCoICPt1TlxI5WNDAsoMxXEho0HLSNKgcHAQemYS7tfa6CL6mcDcOw-WxRLC-307TjlT140K7BZxiy4Y22zjTtlQoQth5jwGFf8GUGLWIOsGkYURK5jmixfEGeevImN9bB6wd-oZD6LTpvgfhae3TUS1U.MGzb82Ukej942AaEpiEBuA';
        $realmId = '9130357849536636';

        $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/{$realmId}/item?minorversion=70";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->post($sandboxApiUrl,$data);
        return $response;
    }

    protected function updateQuickBooksItem($data)
    {
        $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..T_ByJCXcoL3oG7hCautcPA.eTj15RbTRr7zKyuhccwoxNxwQpylt9rJ4JJZmsm51XEbMKl1dRnZLqX4KjVIjRJ0E_rBT6nO7ANpl_H2ZE60OfAyCPbXNky2SUjlqiGKnPxj07F72zvht7dPY6O86f4WkuFAmREzPgIHalD1wIoS1a_t_-sj5JhO-NZXPA6P-vjMm5fc3tvNNntZykoz1QYXmzL7MJXCJY5VjDliZetCn3vCa_-zho_pT-oz2TLLmvk6iTax60jFK2CXMjlrPUm3O6yBVsMy9Ov7aOO0vuxHIQlVbvtH-1M8RpSCzYsnSGUSjAA7LItXEmw0fMOOnCcVHkikuriwgN__OdudfvGY4WYBmKwIADOB_9Zfq26cHvk8lhgnaMddlm8C0R0RkrNZuYa33q04TCJbrGbBkZAecbPWDCf3l6uYJ6oKtEGC4hHvUWh4aj6InrSZW82agNfqfEu3UpwtT4YZKlxO2cJScDviRi0UzwhP9abrP8RhQgsrcn-3Q59FPGZNjr7J4-KLXIQ1JYjNRyRNAb6NEPCPxGCzj5R2zXSykQLO8e7N1wAPdNlaqnjOYEhKt9L4VlQrBT68bVnvsmZvuMWSmp-QkLnhwgX4uEW9ULHMsvD4QDCp1fwNz6PgabLZPZh-DySbEEM1sWNcRgd37uLlHJLleT7aUYE6Qs4WeDAJxJINNjCRgeHi68yhKNLHFz7yftvVboUP8cioH1ot_TNRMzFYjEWEQbAqrNvuC97miIJt7V9--yXGWCZpMZCbuEw8GXDR.SdS8rLRME9GDoxsnLnJvYw';
        $realmId = '9130357849536636';
        $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/{$realmId}/item";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($sandboxApiUrl, $data);

        return $response;
    }

    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $data['menu'] = "products";
        $data['menu_sub'] = "";
        $data['product'] = $product;

        return view('products.edit', $data);

    }

    public function update(Request $request, $id)
    {
        $user_id = Auth::user()->id;

        try {
            DB::beginTransaction();
            $dataService = QBDataService::init();
            // Your existing logic to update in the local database
            $product = Product::findOrFail($id);

            $syncToken = $product->SyncToken;

            $data = [
                'Name' => $request->input('name'),
                'Description' => $request->input('description'),
                'UnitPrice' => $request->input('unit_price'),
                'IncomeAccountRef' => [
                    'name' => 'Sales of Product Income',
                    'value' => '79',
                ],
                'ItemCategoryType'=>'Service',
                'AssetAccountRef' => [
                    'name' => 'Inventory Asset',
                    'value' => '81',
                ],
                'Type' => $request->input('type'),
                'SyncToken'=>$syncToken,
                'ExpenseAccountRef' => [
                    'name' => 'Cost of Goods Sold',
                    'value' => '80',
                ],
            ];
            $ItemId=$product->ItemId;
            $productToUpdate = $dataService->Query("SELECT * FROM Item WHERE id='$ItemId'");

           // echo '<pre>';print_r($productToUpdate);exit();
            $theProduct='';
            if(!empty($productToUpdate) && sizeof($productToUpdate) == 1){
                $theProduct = current($productToUpdate);
            }

            $quickbooksResponse = \QuickBooksOnline\API\Facades\Item::update($theProduct,$data);
           //echo '<pre>';var_dump($quickbooksResponse);exit();
           // dd($quickbooksResponse->json());
            if ($quickbooksResponse) {
                // Your existing logic to update in the local database with QuickBooks data
                $product->update([
                    "id"=>$id,
                    'ItemId' => $quickbooksResponse->Id,
                    'Name' => $quickbooksResponse->Name,
                    'Description' => $quickbooksResponse->Description ?? null,
                    'Active' => $quickbooksResponse->Active,
                    'FullyQualifiedName' => $quickbooksResponse->FullyQualifiedName,
                    'Taxable' => $quickbooksResponse->Taxable ?? null,
                    'UnitPrice' => $quickbooksResponse->UnitPrice ?? null,
                    'Type' => $quickbooksResponse->Type ?? null,
                    'IncomeAccountRef' => isset($quickbooksResponse->IncomeAccountRef) ? json_encode($quickbooksResponse->IncomeAccountRef) : null,
                    'PurchaseCost' => $quickbooksResponse->PurchaseCost ?? null,
                    'TrackQtyOnHand' => $quickbooksResponse->TrackQtyOnHand ?? null,
                    'SyncToken' => $quickbooksResponse->SyncToken,
                    'updatedby' => $user_id,
                ]);
                DB::commit();

                return redirect()->route('products.index')->with('success', 'Product updated successfully');
            } else {
                DB::rollBack();
                return redirect()->route('products.edit', ['id' => $id])->with('error', 'Something went wrong!');
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('products.edit', ['id' => $id])->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('products.edit', ['id' => $id])->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }


    public function destroy(Product $product,$id)
    {
        $product = Product::find($id);
        $product->delete();
        toast('Product deleted successful!', 'success');
        return redirect()->route('products.index');
    }


    public function syncItems()
    {
        try {
            $user_id = Auth::user()->id;

            $realmId = '9130357849536636';

            $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..gWtNOQ_npS-RSgrXYiPwYg.67ywnTiXUUt2NgXdT9GinH-9DUFHKgwYRlnlap1uVs-8Ihmm0Q5hhzRRLys9QWI6_y-KkZquQe1OChlUX5grnvEONy9efsRPAAnL4zl4m9j_5ARt79s1warmVuMOUego9XWrvRpVWZWWiqhelhw-lvT2--cgkOvoIIIytd1GaWGQzTh9j0ulVhczUDx_7esjHuKjbugwT_DrmK3sxbDjT1yes7411PILCdhgrNAKkZmNuR_AMAAGdIV0kEFBb-JS1pCALqtdtHWimKzVXppMtEYdGAFFaW5qjas4Vg5ABWLXBFqs-iyLbeMqtqh3VIlb3OdV4bQm07RGhp6-LUViTJXaLZeHNmPTrjZKz_GT690JEvFichHUkFpSpTMOkQViaaXJO4O5vc8KP6sdq9H_1pL4OywXFXcnWYiYUgPUazORkoyxhXPqwl0lSbh9ETsATflp7W2DZ3gYMbswJ7tEjGxvuNr_Y3ztY2VlSILSlTYzC1F41euiofNHJOHHjfqJF7CmK2L88Yg1SRsmPdxUnEJG-UzhlYkZeN1TleCTBj0Y_7BXeNVE1UgyLfyuutPvNR3lafgL1rx3m6RaMArsuHewx_Z_gS5ZhSgMw1jkMXsIbMLzHyf2NoBT-y8_mvnBe_6-hlgPlYt1DKIL9F7Ay8TW6klvUbI3sJmOmxBf1ktBmn6lbRyXcUjUM8yKzuNuvteB-mtUpbdCZlsBLPpGoqCdLCuvaTqABF1udA6RgXPCxScP52ma8pvfpddwPDJb.VjcBz6R9BcLDK1y9uPrmpA';

            $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/{$realmId}/query?query=select * from Item&minorversion=40";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($sandboxApiUrl);

            $items = $response->json()['QueryResponse']['Item'] ?? [];



            foreach ($items as $item) {

                Product::updateOrCreate(
                    ['ItemId' => $item['Id']],
                    [
                        'Name' => $item['Name'],
                        'Description' => $item['Description'] ?? null,
                        'Active' => $item['Active'],
                        'FullyQualifiedName' => $item['FullyQualifiedName'],
                        'Taxable' => $item['Taxable'] ?? null,
                        'UnitPrice' => $item['UnitPrice'] ?? null,
                        'Type' => $item['Type'] ?? null,
                        'IncomeAccountRef' => isset($item['IncomeAccountRef']) ? json_encode($item['IncomeAccountRef']) : null,                        'PurchaseCost' => $item['PurchaseCost'] ?? null,
                        'TrackQtyOnHand' => $item['TrackQtyOnHand'] ?? null,
                        'domain' => $item['domain'] ?? null,
                        'sparse' => $item['sparse'] ?? null,
                        'SyncToken' => $item['SyncToken'],
                        'createdby' => $user_id,
                        'updatedby' => $user_id,
                    ]
                );
            }

            return redirect()->route('products.index')->with('success', 'Items synchronized successfully');
        } catch (QueryException $e) {
            return redirect()->route('products.index')->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    // Your actual implementation of getAccessToken
    private function getAccessToken($clientId, $clientSecret, $realmId)
    {

        $response = Http::post(config('app.url') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => 'moshahed.alam@nexdecade.com',
            'password' => 'Quick@#$2020',
            'scope' => '',
        ]);

        return $response->json()['access_token'];
    }

    public function autocomplete(Request $request)
    {
        $term = $request->input('product_name');

        // Use the "limit" method to restrict the number of results
        $products = Product::where('Name', 'LIKE', '%' . $term . '%')->limit(10)->get(['id', 'Name','UnitPrice']);

        return response()->json($products);
    }

}
