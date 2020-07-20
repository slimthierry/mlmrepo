<?php

namespace Drewlabs\Contracts\Jwt;

interface IClaims
{

 /**
  * Returns the payload issuer claim value
  *
  * @return string
  */
    public function getiss();

    /**
     * Returns the payload issuer claim value with https protocol
     *
     * @return string
     */
    public function getissSSL();

    /**
     * Set the token ttl (in minutes).
     *
     * @param  int  $ttl
     * @return static
     */
    public function setTTL(int $ttl);

    /**
     * Get the token ttl.
     *
     * @return int
     */
    public function getTTL(): int;

    /**
     * Return the list of default claims to be set on payloads
     *
     * @return array
     */
    public function getDefaultClaims();

    /**
     * Returns a payload from the default claims
     *
     * @param array|null $custom_claims_list
     * @return array
     */
    public function toPayload(array $custom_claims_list = null);
}
