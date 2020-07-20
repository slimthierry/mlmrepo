<?php

namespace Drewlabs\Contracts\Jwt;

interface IBlacklist
{

 /**
  * Add the token (jti claim) to the blacklist.
  * @param array|object $payload
  * @return bool
  */
    public function add($payload);

    /**
     * Determine whether the token has been blacklisted.
     * @param array|object $payload
     * @return bool
     */
    public function has($payload);

    /**
     * Remove the token (jti claim) from the blacklist.
     * @param array|object $payload
     * @return bool
     */
    public function remove($payload);

    /**
     * Refresh or reinitialise tokens blacklist
     * @return bool
     */
    public function clear();
}
