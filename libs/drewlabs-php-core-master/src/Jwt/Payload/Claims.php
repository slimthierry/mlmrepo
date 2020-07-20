<?php

namespace Drewlabs\Core\Jwt\Payload;

use Drewlabs\Contracts\Jwt\IClaims;
use Drewlabs\Utils\DateUtils;

class Claims implements IClaims
{
    /**
     * Payload issuer http ressource which is unique for each platform issuing the jwt token
     * @var string|mixed $iss
     */
    protected $iss;

    /**
     * Payload issuer https ressource which is unique for each platform issuing the jwt token
     */
    protected $iss_ssl;

    /**
     * Refresh time to live of a token
     *
     * @var integer
     */
    protected $ttl;

    /**
     * List of the token default claims that are mandatory
     *
     * @var array $default_claims
     */
    protected $default_claims = array(
        ClaimTypes::OAUTH2_ISSUER,
        ClaimTypes::OAUTH2_ISSUER_SSL,
        //   ClaimTypes::OAUTH2_ISSUE_AT,
        ClaimTypes::OAUTH2_EXPIRATION,
        ClaimTypes::OAUTH2_NOT_BEFORE,
        ClaimTypes::OAUTH2_JIT,
    );

    /**
     * Payload claim object initialiser
     *
     * @param string $issuer_claim
     * @param string $issuer_claim_ssl
     */
    public function __construct($issuer_claim, $issuer_claim_ssl, $ttl = 360)
    {
        $this->iss = $issuer_claim;
        $this->iss_ssl = $issuer_claim_ssl;
        $this->ttl = $ttl;
    }

    /**
     * Returns the payload issuer claim value
     *
     * @return string
     */
    public function getiss()
    {
        return $this->iss;
    }

    /**
     * Returns the payload issuer claim value with https protocol
     *
     * @return string
     */
    public function getissSSL()
    {
        return $this->iss_ssl;
    }

    /**
     * Set the Issued At (iat) claim.
     *
     * @return int
     */
    private function iat(): int
    {
        return DateUtils::now()->getTimestamp();
    }

    /**
     * Set the Expiration (exp) claim.
     *
     * @return int
     */
    private function exp(): int
    {
        return DateUtils::now()->add_minutes($this->ttl)->getTimestamp();
    }

    /**
     * Set the Not Before (nbf) claim.
     *
     * @return int
     */
    private function nbf(): int
    {
        return DateUtils::now()->getTimestamp();
    }

    /**
     * Set a unique id (jti) for the token.
     *
     * @return string
     */
    private function jti(): ? string
    {
        return (new \Tuupola\Base62)->encode(random_bytes(32));
    }

    /**
     * Set the token ttl (in minutes).
     *
     * @param  int  $ttl
     * @return static
     */
    public function setTTL(int $ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * Get the token ttl.
     *
     * @return int
     */
    public function getTTL(): int
    {
        return $this->ttl;
    }

    /**
     * Return the list of default claims to be set on payloads
     *
     * @return array
     */
    public function getDefaultClaims()
    {
        $claims = isset($this->default_claims) ? $this->default_claims : array();
        return array_merge($claims, array(ClaimTypes::OAUTH2_ISSUE_AT));
    }

    /**
     * Returns a payload from the default claims
     *
     * @param array|null $custom_claims_list
     * @return array
     */
    public function toPayload(array $custom_claims_list = null)
    {
        $payload = array(
            ClaimTypes::OAUTH2_ISSUER => $this->getiss(),
            ClaimTypes::OAUTH2_ISSUER_SSL => $this->getissSSL(),
            ClaimTypes::OAUTH2_EXPIRATION => $this->exp(),
            ClaimTypes::OAUTH2_NOT_BEFORE => $this->nbf(),
            ClaimTypes::OAUTH2_JIT => $this->jti(),
        );
        if (isset($custom_claims_list) && !empty($custom_claims_list)) {
            $custom_claims_payload = array();
            foreach ($custom_claims_list as $key => $value) {
                // Removes non-assoc entries and check if key is in the default claims
                if (is_int($key) || array_key_exists($key, $this->default_claims)) {
                    continue;
                }
                $custom_claims_payload[$key] = $value;
            }
            $payload = array_merge($custom_claims_payload, $payload);
        }
        // Set the issue_at token if not exist in the custom_claims
        isset($payload[ClaimTypes::OAUTH2_ISSUE_AT]) ?: $payload[ClaimTypes::OAUTH2_ISSUE_AT] = $this->iat();
        return $payload;
    }
}
