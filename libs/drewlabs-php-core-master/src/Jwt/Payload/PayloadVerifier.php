<?php

namespace Drewlabs\Core\Jwt\Payload;

use Drewlabs\Contracts\Jwt\IPayloadFactory;
use Drewlabs\Utils\Str;

class PayloadVerifier
{
    /**
     * Payload factory instance provider
     *
     * @var IPayloadFactory
     */
    protected $payload_factory;

    /**
     * PayloadVerifier interface object initializer
     *
     * @param IPayloadFactory $factory
     */
    public function __construct(IPayloadFactory $factory)
    {
        $this->payload_factory = $factory;
    }

    /**
     * Check if payload generated from the token decode operation has valid values
     *
     * @param array $payload
     * @return bool
     */
    public function verify(array $payload)
    {
        // Provide more checks if needed in the future
        $claims = $this->payload_factory->getClaims();
        if (count(array_intersect(array_keys($payload), $claims->getDefaultClaims())) !== count($claims->getDefaultClaims())) {
            return false;
        }
        if (!is_string($payload[ClaimTypes::OAUTH2_ISSUER]) || !Str::is_same($payload[ClaimTypes::OAUTH2_ISSUER], $claims->getiss())) {
            return false;
        }
        if (!is_string($payload[ClaimTypes::OAUTH2_ISSUER_SSL]) || !(Str::starts_with($payload[ClaimTypes::OAUTH2_ISSUER_SSL], "https://")) || !Str::is_same($payload[ClaimTypes::OAUTH2_ISSUER_SSL], $claims->getissSSL())) {
            return false;
        }
        return true;
    }
}
