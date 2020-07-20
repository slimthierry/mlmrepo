<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SocialAuthenticationsController extends \Drewlabs\Packages\Http\Controllers\ApiController
{

    /**
     * Handle GET /authsocial/google
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function authWithGoogle(Request $request)
    {
        // $config = (new \SocialiteProviders\Manager\Config(
        //     env('GOOGLE_CLIENT_ID'),
        //     env('GOOGLE_CLIENT_SECRET'),
        //     env('GOOGLE_CALLBACK_URL')
        // ))->get();
        // return $this->respondOk(app()[Factory::class]->buildProvider(GoogleProvider::class, $config)->scopes([Google_Service_People::CONTACTS_READONLY])->stateless()->getGoogleAuthUrl());
    }

    /**
     * Handle GET /auth/social/google/callback
     *
     * @param Request $request
     * @return void
     */
    public function handleGoogleAuthCallback(Request $request)
    {
        return view('social.successful_auth', []);
    }

    /**
     * Handle GET /auth/social/google/user
     *
     * @param Request $request
     * @return void
     */
    public function getGoogleAuthUser(Request $request)
    {
        // $config = (new \SocialiteProviders\Manager\Config(
        //     env('GOOGLE_CLIENT_ID'),
        //     env('GOOGLE_CLIENT_SECRET'),
        //     env('GOOGLE_CALLBACK_URL')
        // ))->get();
        // return $this->respondOk(app()[Factory::class]->buildProvider(GoogleProvider::class, $config)->scopes([Google_Service_People::CONTACTS_READONLY])->stateless()->user());
    }
}
