<?php

namespace App\Http\Controllers;

use App\Product;
use App\QBDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $products = Product::where('createdby',$user_id)->get();
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $data['qb_products'] = $products;



        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $productArray = [
            'Name' => $request->input('name'),
            'ItemId' => rand(1,1000),
            'Description' => $request->input('description'),
            'Type' => $request->input('type'),
            'UnitPrice' => $request->input('unit_price'),
            'Active' => true,
            'TrackQtyOnHand' => true,
            'createdby' => 2,
            'updatedby' => 2,
            // Add other fields based on your local database schema
        ];

        $product = Product::create($productArray);

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
        dd($quickbooksResponse);
        if ($quickbooksResponse->successful()) {

            $quickbooksItemId = $quickbooksResponse->json()['Item']['Id'];

            $product->update(['quickbooks_id' => $quickbooksItemId]);

            return redirect()->route('products.index')->with('success', 'Product created successfully');
        }

    }
    private function createQuickBooksItem(array $data)
    {

        $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..fINrXgR1GfEvceGaGREfwQ.-iBu8QhNB9aEuAs-J5FQ445-1JqGNHYlMz_qmdPfaBS0Q1xa77CpdvpGuyLW5GAGgCk1_mFB7GKkhHqGcUs_w9UD3dOYj0VjfOWUu5mfhcmAHLFFbMZ6hkWNrk4BqiN0Cr0kKWaH9T4jOCpvIJeEIrLaXnOZxZ25XauL-DwnNIWz8VOMm3ofx1RNLogtOMySC1iA3x3yuWyp3YAwTgDAG2IaFRm5iVo2fuVpbya5VvRrp8WUE2zkYN-C0eBNdmhsnC3O1Ha98cmtP5Jgb-YS8InZ2Z5nLrveC_zMhCA7FGVjcIUyMtPGvZwjV5JlbQ1N2SatkyhTGswaC6bdGFoDwqCp_Czwc6mVqhDM2a63HSauMZGEX5yi-uaRc8o-Acq4LhoEVEKd_OA5qtbimtvCR-zbWd4NBkb4cIzzqCng5wGcCYN6GIiVmyy3S0bvIEWIPwHl1psA7Kqh7zc35_nGHBvRE9ZdrbLPvCGyF88oqzPjPwRiE62j0pRgZ1oFWaSE3-1bGnr4jIxs9_Bxml0EX6LLN_h0goOMYkdKb_ThAfP5-1MuTETP-y3UCQRN-vnWF7Eb0rnDrlMDLj9Z4mxasRkAiRrv8bT2iEEzPRrWW-gBtJdmScLrhkFCdIBSV1EE-OAhBLuJt3gLgVPS6sNTMRJZfMJp5sz2D7mgpalHCVzk6-ED4k59YFIG56kc78HWGGqZG7b0yl-jAtFkyvawNWWbqDFFcgHp9aESrwLkSA3ZFiAzHxJboxRuUrC8O4Px.B1WqNszs6puiDBNcd_ovUQ';
        $realmId = '9130357849536636';

        $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/{$realmId}/item?minorversion=70";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->post($sandboxApiUrl,$data);
        return $response;
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
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
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
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
}
