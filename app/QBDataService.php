<?php


namespace App;


use QuickBooksOnline\API\DataService\DataService;

class QBDataService
{
    public static function init()
    {
        $user = auth()->user();
       // echo '<pre>';print_r($user);exit();

        $setting = $user->setting;
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $setting['ClientID'],
            'ClientSecret' =>  $setting['ClientSecret'],
            'RedirectURI' => $setting['RedirectURI'],
            'scope' => $setting['scope'],
            'baseUrl' => $setting['baseUrl']
        ));

        /*
         * Retrieve the accessToken value from session variable
         */
        $qbAuth = $user->qbAuth;

        $accessToken = $qbAuth->refreshToken(); // we are re generating accrss token.

        $dataService->updateOAuth2Token($accessToken);

        return $dataService;
    }
}
