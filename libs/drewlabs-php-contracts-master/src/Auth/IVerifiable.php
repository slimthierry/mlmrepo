<?php

namespace Drewlabs\Contracts\Auth;

/**
 * @package \Drewlabs\Contracts
 * Implemented class will provide functionnalities for checking user verification state
 */
interface IVerifiable
{
    /**
     * Returns a boolean value indicationg whether the user is verified / Not
     *
     * @return boolean
     */
    public function isVerified();
}
