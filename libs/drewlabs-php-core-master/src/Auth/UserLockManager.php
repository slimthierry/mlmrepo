<?php

namespace Drewlabs\Core\Auth;

use Drewlabs\Contracts\Auth\IAccountLockManager;
use Drewlabs\Contracts\Auth\IUserModel as User;
use Drewlabs\Utils\DateUtils;

class UserLockManager implements IAccountLockManager
{



    /**
     * @var int
     */
    private $max_attempts = 5;

    /**
     * Return the Account Lockout timeout in minutes
     *
     * @return int
     */
    public static function getLockTimeoutInMinutes()
    {
        return 60;
    }

    /**
     * @inheritDoc
     */
    public function setMaxAttempts(int $value)
    {
        $this->max_attempts = $value;
        return $this;
    }

    /**
     * Check if an authenticatable account is locked
     *
     * @param User $user
     * @return bool
     */
    public function isLocked($user)
    {
        if (is_null($user)) {
            throw new \RuntimeException('[$user] parameter should not be null');
        }
        if ($user->{$user->getLockExpireAt()} === null) {
            return false;
        }
        if ((intval($user->{$user->getLockEnabled()}) === 1) && (DateUtils::from_timestamp(strtotime($user->{$user->getLockExpireAt()})))->is_future()) {
            return true;
        }
        return false;
    }

    /**
     * Remove the lock on a given authenticable user
     *
     * @param User $user
     * @return void
     */
    public function removeLock($user)
    {
        $user->{$user->getLockEnabled()} = 0;
        $user->{$user->getLockExpireAt()} = null;
        $user->{$user->getLoginAttempts()} = 0;
        $user->{'save'}();
    }

    /**
     * Put a lock on a given user
     *
     * @param User $user
     * @return void
     */
    public function lock($user)
    {
        $user->{$user->getLockEnabled()} = 1;
        $user->{$user->getLockExpireAt()} = date('Y-m-d H:i:s', DateUtils::from_timestamp(time())->add_minutes(static::getLockTimeoutInMinutes())->getTimestamp());
        $user->{$user->getLoginAttempts()} = 0;
        $user->{'save'}();
    }

    /**
     * Increments the account failure attempts
     *
     * @param User $user
     * @return void
     */
    public function incrementFailureAttempts($user)
    {
        if ($user->{$user->getLoginAttempts()} >= $this->max_attempts) {
            $this->lock($user);
        } else {
            $user->{$user->getLoginAttempts()} = $user->{$user->getLoginAttempts()} + 1;
            $user->{'save'}();
        }
    }
}
