<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use QuickBooksOnline\API\DataService\DataService;

class Auth extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getToken()
    {
        if (empty($this->token)) {
            return;
        }

        return unserialize($this->token);
    }

    public function setToken($token)
    {
        $this->token = serialize($token);
        $this->save();
    }

    public function refreshToken()
    {
        $setting = \Illuminate\Support\Facades\Auth::user()->setting;

        $accessToken = $this->getToken();
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $setting['ClientID'],
            'ClientSecret' =>  $setting['ClientSecret'],
            'RedirectURI' => $setting['RedirectURI'],
            'baseUrl' => $setting['baseUrl'],
            'refreshTokenKey' => $accessToken->getRefreshToken(),
            'QBORealmID' => $setting['QBORealmID'],
        ));

        /*
         * Update the OAuth2Token of the dataService object
         */
        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
        $dataService->updateOAuth2Token($refreshedAccessTokenObj);

        $this->setToken($refreshedAccessTokenObj);

//    print_r($refreshedAccessTokenObj);
        return $refreshedAccessTokenObj;
    }
}
