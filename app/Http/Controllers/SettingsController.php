<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings;
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
//        $data['settings'] = auth()->user()->setting;
        $data['settings'] = Settings::find(1);
        $data['qbauth'] = $this->qbInvoke();
        return view('settings.index', $data);
    }

    public function edit(Settings $id)
    {
        $data = [];
        $data['menu'] = "settings";
        $data['menu_sub'] = "";
        $data['settings'] = $id;
        return view('settings.edit', $data);
    }

    public function store(Settings $settings, Request $request)
    {

        $settings->ClientID = $request->ClientID;
        $settings->ClientSecret = $request->ClientSecret;
        $settings->RedirectURI = $request->RedirectURI;
        $settings->save();

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
