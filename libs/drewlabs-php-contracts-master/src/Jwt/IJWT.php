<?php

namespace Drewlabs\Contracts\Jwt;

interface IJWT
{

 /**
  * Convertis et signe un objet PHP ou un tableau en une chaine de charactère JWT
  *
  * @param object|array  $payload    PHP object or array
  *
  * @return string A signed JWT
  *
  */
    public function encode($payload);

    /**
     * Convertis une chaine charactère JWT en un objet PHP
     *
     * @param string        $jwt            The JWT
     * @param string|array  $secretKey      The key, or map of keys.
     *                                      If the algorithm used is asymmetric, this is the public key
     *
     * @return array|object The JWT's payload as a PHP object
     */
    public function decode($token);
}
