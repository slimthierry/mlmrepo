<?php

namespace Drewlabs\Packages\Identity;

use Illuminate\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Packages\Identity\Contracts\IAuthManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Drewlabs\Core\Observable\SubjectProvider;

class AuthManager implements IAuthManager
{
    /**
     * Authenticated user instance
     * @var IAuthenticatable|Authenticatable
     */
    private $authenticatable;

    /**
     * Authenticatable provider class instance
     *
     * @var IAuthenticatableProvider
     */
    private $provider;

    /**
     * Auth guard associated with the current request
     *
     * @var Guard
     */
    private $guard;

    /**
     *
     * @var SubjectProvider
     */
    private $loginattemptSubjectProvider;

    public function __construct(IAuthenticatableProvider $provider, Guard $guard, SubjectProvider $loginattemptSubjectProvider)
    {
        $this->provider = $provider;
        $this->guard = $guard;
        $this->loginattemptSubjectProvider = $loginattemptSubjectProvider;

    }

    /**
     * @inheritDoc
     */
    public function authenticate(array $credentials, bool $remember)
    {
        $status = 0;
        if (count($credentials) == 0) {
            throw new \RuntimeException('Authentication credentials must be an array');
        }
        $user = $this->provider->findByCrendentials($credentials);
        # region Added mail mail login credentials validation
        $identifier = \Drewlabs\Packages\Identity\User::getUserUniqueIdentifier();
        if (is_null($user) && filter_var($credentials[$identifier], FILTER_VALIDATE_EMAIL)) {
            $userInfo = \Drewlabs\Packages\Identity\UserInfo::with(['user'])->where('email', $credentials[$identifier])->get()->first();
            if (is_null($userInfo)) {
                return false;
            }
            $user = $userInfo->user->toAuthenticatable();
        }
        # end region Added mail mail login credentials validation
        if (is_null($user) || !($user instanceof IAuthenticatable)) {
            return false;
        }
        $validCredentials  = $this->provider->validateAuthCredentials($user, $credentials);
        if ($validCredentials) {
            // If user exists, if it credentials are valid... And if the remember me is set, generate remember token for the user
            if ($remember) {
                $token = Str::random(60);
                $this->provider->updateAuthRememberToken($user, $token);
                $user->rememberToken($token);
            }
            $this->authenticatable = $user;
            // Fire an event with authenticated data
            $status = 1;

        }
        // Fire an event with unauthenticated data
        $this->loginattemptSubjectProvider->fire([
            User::getUserUniqueIdentifier() => $credentials[User::getUserUniqueIdentifier()],
            'status' => $status,
        ]);
        return $validCredentials;
    }

    /**
     * @inheritDoc
     */
    public function logout(Request $request)
    {
        return $request->user()->token()->revoke();
    }

    /**
     * @inheritDoc
     */
    public function authenticateViaToken($id, $token)
    {
        $user = $this->provider->findByToken($id, $token);
        if ($user) {
            $this->authenticatable = $user;
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function user()
    {
        return $this->authenticatable;
    }
}
