<?php

namespace App\Http\Controllers;

use App\Product;
use App\QBDataService;
use Illuminate\Http\Request;
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
        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        // form
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $dataService = QBDataService::init();
        $data['product'] = $dataService->FindById('item', $id);
        $data['coas'] = $dataService->Query("SELECT * FROM Account");
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
        $user = auth()->user();
        $user->products()->create($request->all());
        toast('Product Associated successful!', 'success');
        return redirect()->route('products.index');
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
