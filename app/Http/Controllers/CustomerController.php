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
            $dataService = QBDataService::init();

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
                'Id'=>date('Ymdhs'),
                'SyncToken'=>1

            ];

          // $quickbooksResponse = $this->createOrUpdateQBCustomers($customerData);
            $quickbooksResponse = \QuickBooksOnline\API\Facades\Customer::create($customerData);

            if ($quickbooksResponse) {
                $customerArray = [
                    'quickbooks_id' => $quickbooksResponse->Id->value,
                    'SyncToken' => $quickbooksResponse->SyncToken,
                    'name' => $quickbooksResponse->DisplayName,
                    'email' => $quickbooksResponse->PrimaryEmailAddr->Address ?? null,
                    'phone' => $quickbooksResponse->PrimaryPhone->FreeFormNumber?? null,
                    'address' => $quickbooksResponse->BillAddr->Line1 ?? null,
                    'city' => $quickbooksResponse->BillAddr->City?? null,
                    'country' => $quickbooksResponse->BillAddr->Country ?? null,
                    'state' => $quickbooksResponse->BillAddr->CountrySubDivisionCode ?? null,
                    'zip' => $quickbooksResponse->BillAddr->PostalCode ?? null,
                    'createdby' => $user_id,
                    'updatedby' => $user_id,
                ];
              //  dd($customerArray);
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
            $dataService = QBDataService::init();

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
            $customerId=$customer->quickbooks_id;
           // $customerToUpdate = $dataService->FindById('Customer', $customerId);

         //   $quickbooksResponse = $this->createOrUpdateQBCustomers($data);
            $quickbooksResponse = \QuickBooksOnline\API\Facades\Customer::update($customerId,$data);


            dd($quickbooksResponse);
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


    public function syncCustomers()
    {
        try {
            $user_id = Auth::user()->id;

            $realmId = '9130357849536636';

            $accessToken = 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..gWtNOQ_npS-RSgrXYiPwYg.67ywnTiXUUt2NgXdT9GinH-9DUFHKgwYRlnlap1uVs-8Ihmm0Q5hhzRRLys9QWI6_y-KkZquQe1OChlUX5grnvEONy9efsRPAAnL4zl4m9j_5ARt79s1warmVuMOUego9XWrvRpVWZWWiqhelhw-lvT2--cgkOvoIIIytd1GaWGQzTh9j0ulVhczUDx_7esjHuKjbugwT_DrmK3sxbDjT1yes7411PILCdhgrNAKkZmNuR_AMAAGdIV0kEFBb-JS1pCALqtdtHWimKzVXppMtEYdGAFFaW5qjas4Vg5ABWLXBFqs-iyLbeMqtqh3VIlb3OdV4bQm07RGhp6-LUViTJXaLZeHNmPTrjZKz_GT690JEvFichHUkFpSpTMOkQViaaXJO4O5vc8KP6sdq9H_1pL4OywXFXcnWYiYUgPUazORkoyxhXPqwl0lSbh9ETsATflp7W2DZ3gYMbswJ7tEjGxvuNr_Y3ztY2VlSILSlTYzC1F41euiofNHJOHHjfqJF7CmK2L88Yg1SRsmPdxUnEJG-UzhlYkZeN1TleCTBj0Y_7BXeNVE1UgyLfyuutPvNR3lafgL1rx3m6RaMArsuHewx_Z_gS5ZhSgMw1jkMXsIbMLzHyf2NoBT-y8_mvnBe_6-hlgPlYt1DKIL9F7Ay8TW6klvUbI3sJmOmxBf1ktBmn6lbRyXcUjUM8yKzuNuvteB-mtUpbdCZlsBLPpGoqCdLCuvaTqABF1udA6RgXPCxScP52ma8pvfpddwPDJb.VjcBz6R9BcLDK1y9uPrmpA';


            $sandboxApiUrl = "https://sandbox-quickbooks.api.intuit.com/v3/company/{$realmId}/query?query=select * from Customer&minorversion=40";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($sandboxApiUrl);

            $customers = $response->json()['QueryResponse']['Customer'] ?? [];

            foreach ($customers as $customer) {
                Customer::updateOrCreate(
                    ['quickbooks_id' => $customer['Id']],
                    [
                        'name' => $customer['DisplayName'],
                        'email' => $customer['PrimaryEmailAddr']['Address'] ?? null,
                        'phone' => $customer['PrimaryPhone']['FreeFormNumber'] ?? null,
                        'address' => $customer['BillAddr']['Line1'] ?? null,
                        'city' => $customer['BillAddr']['City'] ?? null,
                        'country' => $customer['BillAddr']['Country'] ?? null,
                        'state' => $customer['BillAddr']['CountrySubDivisionCode'] ?? null,
                        'zip' => $customer['BillAddr']['PostalCode'] ?? null,

                    ]
                );
            }

            return redirect()->route('customers.index')->with('success', 'Customers synchronized successfully');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

}
