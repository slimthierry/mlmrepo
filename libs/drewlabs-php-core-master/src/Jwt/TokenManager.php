<?php

namespace Drewlabs\Core\Jwt;

use Drewlabs\Contracts\Jwt\IBlacklist;
use Drewlabs\Contracts\Jwt\IPayloadFactory;
use Drewlabs\Contracts\Jwt\IJWT;
use Drewlabs\Contracts\Jwt\ITokenManager;
use Drewlabs\Core\Jwt\Exceptions\TokenBlacklistedException;
use Drewlabs\Core\Jwt\Exceptions\TokenExpiredException;
use Drewlabs\Core\Jwt\Payload\ClaimTypes;
use Drewlabs\Utils\DateUtils;

class TokenManager implements ITokenManager
{
    /**
     * Base64 encoded string containning connected user information, the issuser and validation data
     *
     * @var string
     */
    protected $token;
    /**
     * Token encoder and decoder provider
     *
     * @var IJWT;
     */
    protected $jwt_provider;
    /**
     * Blacklist provider
     *
     * @var IBlacklist
     */
    protected $blacklist;
    /**
     * Payload factory provider
     *
     * @var IPayloadFactory
     */
    protected $factory;

    /**
     * Control the state of the use of blacklist or not
     *
     * @var boolean
     */
    protected $blacklist_enabled = true;

    public function __construct(
        IJWT $jwt_provider,
        IBlacklist $blacklist,
        IPayloadFactory $factory,
        $blacklist_enabled = true
    ) {
        $this->jwt_provider = $jwt_provider;
        $this->blacklist = $blacklist;
        $this->factory = $factory;
        $this->blacklist_enabled = $blacklist_enabled;
    }


    /**
     * @inheritDoc
     */
    public function decodeToken($token)
    {
        $payload = (array) $this->jwt_provider->decode($token);
        if ($this->blacklist_enabled && ($this->blacklist->has($payload))) {
            throw new TokenBlacklistedException('Unable to decode the token, it has been blacklisted');
        }
        return $payload;
    }

    /**
     * @inheritDoc
     */
    public function encodeToken($payload)
    {
        $this->token = $this->jwt_provider->encode((array) $payload);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function refreshToken($token)
    {
        $payload = $this->decodeToken($token);
        if (DateUtils::from_timestamp($payload[ClaimTypes::OAUTH2_ISSUE_AT])->add_minutes($this->blacklist->refresh_ttl)->is_past()) {
            throw new TokenExpiredException('Cannot refresh token, refresh time expired');
        }
        $this->blacklist->add($payload);
        return $this->encodeToken(
            $this->factory->make(
                array(ClaimTypes::OAUTH2_SUBJECT => $payload[ClaimTypes::OAUTH2_SUBJECT], ClaimTypes::OAUTH2_ISSUE_AT => $payload[ClaimTypes::OAUTH2_ISSUE_AT]),
            )->resolve()
        );
    }

    /**
     * @inheritDoc
     */
    public function invalidateToken($token)
    {
        return $this->blacklist->add($this->decodeToken($token));
    }

    /**
     * Payload factory getter
     *
     * @return IPayloadFactory
     */
    public function payloadFactory()
    {
        return $this->factory;
    }

    /**
     * Token Blacklist provider getter method
     *
     * @return IBlacklist
     */
    public function getBlackList()
    {
        return $this->blacklist;
    }

    /**
     * Return the generated token string
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
