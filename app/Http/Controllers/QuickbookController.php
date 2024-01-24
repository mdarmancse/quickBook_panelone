<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;
use QuickBooksOnline\API\Exception\ServiceException;

class QuickbookController extends Controller
{
    public function callback(User $user, Request $request)
    {
        $setting = $user->setting;
        $qbAuth = $user->qbAuth;
        // Create SDK instance
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $setting['ClientID'],
            'ClientSecret' =>  $setting['ClientSecret'],
            'RedirectURI' => $setting['RedirectURI'],
            'scope' => $setting['scope'],
            'baseUrl' => $setting['baseUrl']
        ));

        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $code = $request->query('code');
        $realmId = $request->query('realmId');
        $setting->QBORealmID = $realmId;
        $setting->save();
        /*
         * Update the OAuth2Token
         */
        try {
            $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($code, $realmId);
        } catch (SdkException | ServiceException $e) {
            dd($e);
        }
        $dataService->updateOAuth2Token($accessToken);

        /*
         * Setting the accessToken for session variable
         */
        $qbAuth->setToken($accessToken);

        return redirect()->route('settings.edit', ['id' => $setting->id]);

    }
}
