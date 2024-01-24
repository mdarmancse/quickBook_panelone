<?php


namespace App;


use QuickBooksOnline\API\DataService\DataService;

class QBDataService
{
    public static function init()
    {
        $user = auth()->user();
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

        /*
         * Update the OAuth2Token of the dataService object
         */
        $dataService->updateOAuth2Token($accessToken);
        //$companyInfo = $dataService->getCompanyInfo();
        /* ====== End QB Authentication ======= */
        return $dataService;
    }
}
