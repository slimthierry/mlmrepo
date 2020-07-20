<?php

namespace  Drewlabs\Packages\Identity\Events\Subscribers;

use Drewlabs\Packages\Identity\User;
use Drewlabs\Core\Observable\SubscriberProvider as Subscriber;
use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Packages\Identity\UserConnexion;

final class LoginAttemptSubscriber extends Subscriber
{
    /**
     * Respond to an Event call
     *
     * @param mixed $credentials
     * @return void
     */
    public function listen($credentials = null)
    {
        $request_ip = app('request')->headers->get('X-Real-IP');
        // Do something with the credentials
        // Create a log connexion for instance
        (new BaseIlluminateModelRepository())->setModel(UserConnexion::class)->insert([
            "usr_connexions_identifier" => $credentials[User::getUserUniqueIdentifier()],
            "usr_connexions_user_connexion_status" => $credentials['status'],
            "usr_connexions_user_connexion_ip_address" => isset($request_ip) ? $request_ip : app('request')->ip(),
        ], true);
    }
}
