<?php

namespace Drewlabs\Contracts\Jwt;

use Drewlabs\Contracts\Factory\IFactory;
use Drewlabs\Contracts\Jwt\IClaims;

interface IPayloadFactory extends IFactory
{
    /**
     * Returns payload claims instance
     *
     * @return IClaims
     */
    public function getClaims();

    /**
     * Set the token ttl (in minutes).
     *
     * @param  int  $ttl
     * @return static
     */
    public function setPayloadTTL(int $ttl);

    /**
     * Set the refresh flow.
     *
     * @param bool $refresh_flow
     * @return $this
     */
    public function setRefreshFlow(bool $refresh_flow = true);
}
