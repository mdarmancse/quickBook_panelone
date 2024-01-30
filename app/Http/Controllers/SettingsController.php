<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Settings;
use Illuminate\Support\Facades\Auth;
use QuickBooksOnline\API\DataService\DataService;

class SettingsController extends Controller
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
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $userId = Auth::user()->id;

        $settings = Settings::where('user_id', $userId)->first();
//        $data['settings'] = auth()->user()->setting;
        $data['settings'] = $settings;
        $data['qbauth'] = $this->qbInvoke();
        return view('settings.index', $data);
    }

    public function edit($id=null)
    {

        $settings=$id?Settings::find($id):Settings::first();
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $data['settings'] = $settings;
        return view('settings.edit', $data);
    }

    public function store(Settings $settings, Request $request)
    {
        $user_id = Auth::user()->id;

        // Update or create a record based on user_id
        $settings->updateOrCreate(['user_id' => $user_id], [
            'ClientID' => $request->input('ClientID'),
            'ClientSecret' => $request->input('ClientSecret'),
            'RedirectURI' => $request->input('RedirectURI'),
            'scope' => $request->input('scope'),
            'baseUrl' => $request->input('baseUrl'),
            'QBORealmID' => $request->input('QBORealmID'),
        ]);

        return back()->with('info', 'Updated successfully!');
    }
    private function qbInvoke()
    {
        $setting = auth()->user()->setting;

        if (!empty($setting['ClientID']) && !empty($setting['ClientSecret'])) {
            $dataService = DataService::Configure(array(
                'auth_mode' => 'oauth2',
                'ClientID' => $setting['ClientID'],
                'ClientSecret' =>  $setting['ClientSecret'],
                'RedirectURI' => $setting['RedirectURI'],
                'scope' => $setting['scope'],
                'baseUrl' => $setting['baseUrl']
            ));

            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

            //Get the Authorization URL from the SDK
            return $OAuth2LoginHelper->getAuthorizationCodeURL();
        }

        return '#';
    }
}
