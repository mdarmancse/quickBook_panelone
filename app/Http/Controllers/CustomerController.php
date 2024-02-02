<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Product;
use App\QBDataService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{

    public function index()
    {
        $user_id = Auth::user()->id;

        $data = [];
        $customers = Customer::orderBy('id', 'DESC')->get();
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $data['customers'] = $customers;

        return view('customers.index', $data);
    }

    public function create($id = null)
    {
        // form
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";

        return view('customers.create', $data);
    }



    public function store(Request $request)
    {
        $user_id = Auth::user()->id;

        try {
            DB::beginTransaction();

            $customerData = [
                'PrimaryEmailAddr' => [
                    'Address' => $request->input('email'),
                ],
                'DisplayName' => $request->input('name'),
                'PrimaryPhone' => [
                    'FreeFormNumber' => $request->input('phone'),
                ],
                'BillAddr' => [
                    'CountrySubDivisionCode' => $request->input('state'),
                    'City' => $request->input('city'),
                    'PostalCode' => $request->input('zip'),
                    'Line1' => $request->input('address'),
                    'Country' => $request->input('country')
                ],

            ];

            $quickbooksResponse = $this->createOrUpdateQBCustomers($customerData);


            if ($quickbooksResponse->successful()) {
                $quickbooksCustomer = $quickbooksResponse->json()['Customer'];

                $customerArray = [
                    'quickbooks_id' => $quickbooksCustomer['Id'],
                    'SyncToken' => $quickbooksCustomer['SyncToken'],
                    'name' => $quickbooksCustomer['DisplayName'],
                    'email' => $quickbooksCustomer['PrimaryEmailAddr']['Address'] ?? null,
                    'phone' => $quickbooksCustomer['PrimaryPhone']['FreeFormNumber'] ?? null,
                    'address' => $quickbooksCustomer['BillAddr']['Line1'] ?? null,
                    'city' => $quickbooksCustomer['BillAddr']['City'] ?? null,
                    'country' => $quickbooksCustomer['BillAddr']['Country'] ?? null,
                    'state' => $quickbooksCustomer['BillAddr']['CountrySubDivisionCode'] ?? null,
                    'zip' => $quickbooksCustomer['BillAddr']['PostalCode'] ?? null,
                    'createdby' => $user_id,
                    'updatedby' => $user_id,
                ];
               // dd($customerArray);
                Customer::create($customerArray);

                DB::commit();

                return redirect()->route('customers.index')->with('success', 'Customer created successfully');
            } else {
                DB::rollBack();
                return redirect()->route('customers.create')->with('error', 'Something went wrong!');
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('customers.create')->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customers.create')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }



    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $data['menu'] = "customers";
        $data['menu_sub'] = "";
        $data['customer'] = $customer;

        return view('customers.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user_id = Auth::user()->id;

        try {
            DB::beginTransaction();

            // Your existing logic to update in the local database
            $customer = Customer::findOrFail($id);

            $syncToken = $customer->SyncToken;

            $data = [
                'Id' => $customer->quickbooks_id,
                'DisplayName' => $request->input('name'),
                'PrimaryEmailAddr' => [
                    'Address' => $request->input('email'),
                ],
                'PrimaryPhone' => [
                    'FreeFormNumber' => $request->input('phone'),
                ],
                'BillAddr' => [
                    'Line1' => $request->input('address'),
                    'City' => $request->input('city'),
                    'Country' => $request->input('country'),
                    'CountrySubDivisionCode' => $request->input('state'),
                    'PostalCode' => $request->input('zip'),
                ],
                'SyncToken' => $syncToken,
            ];

            $quickbooksResponse = $this->createOrUpdateQBCustomers($data);


          //  dd($quickbooksResponse->json());
            if ($quickbooksResponse->successful()) {
                $quickbooksCustomer = $quickbooksResponse->json()['Customer'];

                $customer->update([
                    'id' => $id,
                    'quickbooks_id' => $quickbooksCustomer['Id'],
                    'name' => $quickbooksCustomer['DisplayName'],
                    'email' => $quickbooksCustomer['PrimaryEmailAddr']['Address'],
                    'phone' => $quickbooksCustomer['PrimaryPhone']['FreeFormNumber'],
                    'address' => $quickbooksCustomer['BillAddr']['Line1'],
                    'city' => $quickbooksCustomer['BillAddr']['City'],
                    'country' => $quickbooksCustomer['BillAddr']['Country'],
                    'state' => $quickbooksCustomer['BillAddr']['CountrySubDivisionCode'],
                    'zip' => $quickbooksCustomer['BillAddr']['PostalCode'],
                    'SyncToken' => $quickbooksCustomer['SyncToken'],
                    'updatedby' => $user_id,
                ]);

                DB::commit();

                return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
            } else {
                DB::rollBack();
                return redirect()->route('customers.edit', ['id' => $id])->with('error', 'Something went wrong!');
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return redirect()->route('customers.edit', ['id' => $id])->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('customers.edit', ['id' => $id])->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    public function syncCustomers()
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

    private function createOrUpdateQBCustomers(array $data)
    {

        $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..yCStBSkRgJA0fw0_sF6Ouw.vNzSaumqfqmeAKsq0Pexo_fjdLRg9SyAnnlp89u39kA4aVeiDakdFX4wnshiS4iy0zVHocqIcdbPWuMwY7K-k3iSRjucDb6Rg42QBXr6dfYn8IaaE8HBiJxOVVjK3-WppKxrqmHCPGJEnzzoO76LCdqTN-rLKjhwAgsdEvdk-CCG2nB7u1YTLV3zjN3iLVWsHg_g0KPKT29CCnpI2J_6bTYvs_W0MuGlUAG-YXbqJWl_ZxjOgGdRaefyNkBATWSE_q33o58zujghWvsO6paaT9reqYOdeUDPJhLzroaoxRVTEyX7XQzIJ3ASwbrbxp2ZqSACk9k-70P9_IsDFWDtA7XS-H2LaWLxgZpNHMJEwcJuZFU4biKAfWxPbTMZjMbBK-VB-URssyuJEeaV5jMrN8CUx7r1RKP97v7L0ogcNc1djJZYTRzxUbkR4-sBirWYd4ZnotGT0wTjexBh7b41NR0FzfJ62pjSif7JYWUMP4qRW8D8kEU23mLA50d4MVaNpkABPTK_QUfOgf1WAD88IPb6Lg0pXzwus1jB4VW-DN2lkWNkixLCAeRK7HNkiVtT2hA45qy37RVtuRwfkOxoYtEky0KkrGDWcCQBuCY6Np1g0ge4K7ChclXyd0l35jsmvH5MidjPpT61wVxfsQ41xIdwjvjxWrwe0YXvNyCZhG-B7cKIqETZ1AuLIICMNjiOm9vzzdj8WqNvKJgAgxM_tFGGud6ZQHZzunpgNWDXvVmkOcp_FxE0qnPTwgJ9HHeu.-kpcfuOftI9ReO7rN-z2tw';
        $realmId = '9130357849536636';

        $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/{$realmId}/customer?minorversion=70";
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/json',
        ])->post($sandboxApiUrl,$data);
        return $response;
    }



}
