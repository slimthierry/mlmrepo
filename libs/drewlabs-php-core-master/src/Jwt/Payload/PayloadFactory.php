<?php

namespace Drewlabs\Core\Jwt\Payload;

use Drewlabs\Contracts\Jwt\IClaims;
use Drewlabs\Contracts\Jwt\IPayloadFactory;

class PayloadFactory implements IPayloadFactory
{

 /**
  * Default payload claims
  *
  * @var IClaims
  */
    protected $claims;

    /**
     * Set to make a token refresh flow
     *
     * @var boolean $refresh_flow
     */
    protected $refresh_flow = false;
    /**
     * Actual token payload
     *
     * @var array|object $payload
     */
    protected $payload;

    /**
     * Payload factory instance initialiser
     *
     * @param IClaims $claim
     */
    public function __construct(IClaims $claims)
    {
        $this->claims = $claims;
    }
    /**
     * Generate a payload with application default claims and user provided custom claims
     *
     * @param array $custom_claims_payload
     * @return IPayloadFactory
     */
    public function make($custom_claims_payload = array())
    {
        $this->payload = $this->claims->toPayload($custom_claims_payload);
        return $this;
    }

    /**
     * Return the constucted payload
     * @return array|object
     */
    public function resolve()
    {
        return $this->payload;
    }

    /**
     * Returns payload claims instance
     *
     * @return IClaims
     * @throws \RuntimeException
     */
    public function getClaims()
    {
        if (isset($this->claims)) {
            return $this->claims;
        }
        throw new \RuntimeException(__CLASS__ . " is not properly constructed ");
    }

    /**
     * Set the token ttl (in minutes).
     *
     * @param  int  $ttl
     * @return static
     */
    public function setPayloadTTL(int $ttl)
    {
        $this->claims = $this->claims->setTTL($ttl);
        return $this;
    }

    /**
     * Set the refresh flow.
     *
     * @param bool $refresh_flow
     * @return $this
     */
    public function setRefreshFlow(bool $refresh_flow = true)
    {
        $this->refresh_flow = $refresh_flow;
        return $this;
    }
}
