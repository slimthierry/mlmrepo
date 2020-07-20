<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Packages\Identity\Traits\AuthenticatableSerializer;

class DefaultAuthenticatableSerializer implements \Drewlabs\Contracts\Auth\IAuthenticatableSerializer
{

    use AuthenticatableSerializer;

    /**
     * {@inheritDoc}
     */
    public function serialize($user)
    {
        $tags = array();
        foreach ($user->{'getFillables'}() as $k) {
            if (in_array($k, $user->{'getGuarded'}())) {
                continue;
            }
            if (property_exists($user, $k)) {
                $tags[$k] = $user->{$k};
            }
        }
        return $tags;
    }
}
