<?php

namespace Drewlabs\Core\Jwt;

use Drewlabs\Contracts\Jwt\IBlacklist;
use Drewlabs\Contracts\Storage\IStorage;
use Drewlabs\Core\Jwt\Payload\ClaimTypes;
use Drewlabs\Utils\DateUtils;

class Blacklist implements IBlacklist
{
    /**
     * @var IStorage
     */
    protected $storage;

    /**
     * Number of minutes from issue date in which a JWT can be refreshed.
     *
     * @var int
     */
    public $refresh_ttl;

    /**
     * @param IStorage  $storage
     */
    public function __construct(IStorage $storage, $refresh_ttl = 20160)
    {
        $this->storage = $storage;
        $this->refresh_ttl = $this->setRefreshTTL($refresh_ttl);
    }

    /**
     * Add the token (jti claim) to the blacklist.
     *
     * @return bool
     */
    public function add($payload)
    {
        $exp = DateUtils::from_timestamp(intval($payload[ClaimTypes::OAUTH2_EXPIRATION]));
        $refresh_exp = DateUtils::from_timestamp($payload[ClaimTypes::OAUTH2_ISSUE_AT])->add_minutes($this->refresh_ttl);

        // No need to blacklist token if already expired
        if ($exp->is_past() && $refresh_exp->is_past()) {
            return false;
        }
        // Set the cache entry's lifetime to be equal to the amount
        $cacheLifetime = $exp->max($refresh_exp)->add_minutes()->min_diff();
        $this->storage->put($payload[ClaimTypes::OAUTH2_JIT], [], $cacheLifetime);
        return true;
    }

    /**
     * Determine whether the token has been blacklisted.
     *
     * @return bool
     */
    public function has($payload)
    {
        return $this->storage->has($payload[ClaimTypes::OAUTH2_JIT]);
    }

    /**
     * Remove the token (jti claim) from the blacklist.
     *
     * @return bool
     */
    public function remove($payload)
    {
        return $this->storage->delete($payload[ClaimTypes::OAUTH2_JIT]);
    }

    /**
     * Remove all tokens from the blacklist.
     *
     * @return bool
     */
    public function clear()
    {
        $this->storage->flush();

        return true;
    }

    /**
     * Set the refresh time limit.
     *
     * @param  int
     *
     * @return $this
     */
    public function setRefreshTTL($ttl)
    {
        $this->refresh_ttl = (int) $ttl;
    }
}
