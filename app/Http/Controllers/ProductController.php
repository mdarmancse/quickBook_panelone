<?php

namespace App\Http\Controllers;

use App\Product;
use App\QBDataService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                'Type' =>  $request->input('type'),
                'ExpenseAccountRef' => [
                    'name' => 'Cost of Goods Sold',
                    'value' => '80',
                ],
            ];

            $quickbooksResponse = $this->createQuickBooksItem($data);



            if ($quickbooksResponse->successful()) {
                $quickbooksItem = $quickbooksResponse->json()['Item'];

                $productArray=     [
                    'ItemId' => $quickbooksItem['Id'],
                    'Name' => $quickbooksItem['Name'],
                    'Description' => $quickbooksItem['Description']??null,
                    'Active' => $quickbooksItem['Active'],
                    'FullyQualifiedName' => $quickbooksItem['FullyQualifiedName'],
                    'Taxable' => $quickbooksItem['Taxable'],
                    'UnitPrice' => $quickbooksItem['UnitPrice'],
                    'Type' => $quickbooksItem['Type'],
                    'IncomeAccountRef' => json_encode($quickbooksItem['IncomeAccountRef']),
                    'PurchaseCost' => $quickbooksItem['PurchaseCost'],
                    'TrackQtyOnHand' => $quickbooksItem['TrackQtyOnHand'],
                    'domain' => $quickbooksItem['domain'],
                    'sparse' => $quickbooksItem['sparse'],
                    'SyncToken' => $quickbooksItem['SyncToken'],
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

            // Your existing logic to update in the local database
            $product = Product::findOrFail($id);

            $syncToken = $product->SyncToken;

            $data = [
                'Id' => $request->input('ItemId'),
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

            $quickbooksResponse = $this->updateQuickBooksItem($data);

            //dd($quickbooksResponse->json());

            if ($quickbooksResponse->successful()) {
                $quickbooksItem = $quickbooksResponse->json()['Item'];

                // Your existing logic to update in the local database with QuickBooks data
                $product->update([
                    'Name' => $quickbooksItem['Name'],
                    'Description' => $quickbooksItem['Description'] ?? null,
                    'Active' => $quickbooksItem['Active'],
                    'FullyQualifiedName' => $quickbooksItem['FullyQualifiedName'],
                    'Taxable' => $quickbooksItem['Taxable'],
                    'UnitPrice' => $quickbooksItem['UnitPrice'],
                    'Type' => $quickbooksItem['Type'],
                    'IncomeAccountRef' => json_encode($quickbooksItem['IncomeAccountRef']),
                    'PurchaseCost' => $quickbooksItem['PurchaseCost'],
                    'TrackQtyOnHand' => $quickbooksItem['TrackQtyOnHand'],
                    'domain' => $quickbooksItem['domain'],
                    'sparse' => $quickbooksItem['sparse'],
                    'SyncToken' => $quickbooksItem['SyncToken'],
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

        // Replace these values with your actual QuickBooks credentials
        $realmId = '9130357849536636';
        $clientId = 'AB5kFbletRbjWcZWUqor6CHxtY730MlAZ9nEcuFNtmjfNwOdtU';
        $clientSecret = 'oW2mxLmn6WgFxQOzKDn9xrSGV4j8i0RkKo6gaAYW';


       // $accessToken = $this->getAccessToken($clientId, $clientSecret, $realmId);
        $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..OcfR6Kcw_lIbzVsMXZ42Bw.3kUINn9uxYlfbho0089ixlO_J8JUbbQdxzzJE61W6iLeoBASNorP6cJL48s-k2pKdnp3OSeeMW9YKo_39ZUkQngT62llTBJQvMHASU2JaNR-KCjRnDgCUrPvyj9B0W2xb6WuNYYZRCGLrhkxxlGyEgJR-FHjZrmabvGMBTdllNCBzZ1lKSvWr8G_6ROYpFuy47b1UGrNx2fq_6HXS0EORq_eTCZbKxldtjsDNEwgCy72wtZDhloQr3Haqf9Qsi49euQVVPTeJzUILkoE3KrdOfDiKD9MCBasoTY6xEImAK_4B5lmi7MwRsv3irK5LChZX13q-Wk3GAf_bXw-kBO29BTuyiCZMZUy_PP5eFENxmjZlb0gT-64vSCMxS8zo62eGHwAxpZYQIQhZXMqar_LBPuBVuH98ErCQrTWfh9Oq-E-y7yPKoHdPYe79c946pBl8ddsAIlJCPIWPIvmo5TSiSezXdUjtFHJfEHeX9qHvJp9kJCzT7UYgUdIKmYbZ3dkZ1BHMqoMsTPg78R4aMBnVdbF40AEBp_71Z_UcxwBUrh9brM3QhwSJ7N-LAW4vsU3Uegy0UE3z0ewzBhmsSnLHY6adFteE4UwEPmWXusWeqfutu0GycwKTdRalE5LqHrUw1UukCMkMFYtdOgOUFNE5vC6HJ2h5S8OEyxkexSkOWGcJWsbozDP9FKDxKiEoN3tA4fQFwIQCwuaZ1ly55DiX05BsE5GQBYVjDNMlKCJPGw.dywjn_KjOIiH7sPJuZYhGA';


        $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/9130357849536636/query?query=select * from Item&minorversion=40";


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->get($sandboxApiUrl);



        $items = $response->json()['QueryResponse']['Item'] ?? [];

//dd($response->json());

        foreach ($items as $item) {
            Product::updateOrCreate(
                ['ItemId' => $item['Id']],
                [
                    'Name' => $item['Name'],
                    'Description' => $item['Description']??null,
                    'Active' => $item['Active'],
                    'FullyQualifiedName' => $item['FullyQualifiedName'],
                    'Taxable' => $item['Taxable'],
                    'UnitPrice' => $item['UnitPrice'],
                    'Type' => $item['Type'],
                    'IncomeAccountRef' => json_encode($item['IncomeAccountRef']),
                    'PurchaseCost' => $item['PurchaseCost'],
                    'TrackQtyOnHand' => $item['TrackQtyOnHand'],
                    'domain' => $item['domain'],
                    'sparse' => $item['sparse'],
                    'SyncToken' => $item['SyncToken'],
                ]
            );
        }


        return response()->json(['message' => 'Items synchronized successfully']);
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
