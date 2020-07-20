<?php

namespace Drewlabs\Contracts\Jwt;

interface IPayloadVerifier
{
    /**
     * Check if payload generated from the token decode operation has valid values
     *
     * @param array $payload
     * @return bool
     */
    public function verify(array $payload);
}
