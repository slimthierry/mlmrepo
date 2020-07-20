<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Contracts\Auth\IUserModel;
use Illuminate\Container\Container;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenFactory;

trait HasApiTokensTrait
{

    /**
     * The current access token for the authentication user.
     *
     * @var \Laravel\Passport\Token
     */
    protected $accessToken;

    /**
     * Get all of the user's registered OAuth clients.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return app(IUserModel::class)->fromAuthenticatable($this)->hasMany(Passport::clientModel(), 'user_id');
    }

    /**
     * Get all of the access tokens for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return app(IUserModel::class)->fromAuthenticatable($this)->hasMany(Passport::tokenModel(), 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the current access token being used by the user.
     *
     * @return \Laravel\Passport\Token|null
     */
    public function token()
    {
        return $this->accessToken;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function tokenCan($scope)
    {
        if (!boolval(\drewlabs_http_handlers_configs('apply_middleware_policies'))) {
            return true;
        }
        $allAuthorization = \drewlabs_identity_configs('all_authorization', \Drewlabs\Packages\Identity\DefaultScopes::SUPER_ADMIN_SCOPE);
        // If the access token is a first party client checks if it has the required permissions
        if ($this->accessTokenCan(\drewlabs_passport_configs('first_party_clients_scope', '*')) && ($this instanceof \Drewlabs\Contracts\Auth\IDrewlabsAuthorizable)) {
            $permissions = array_map(function($item) {
                return is_string($item) ? $item : $item->label;
            }, $this->getPermissions());
            return !empty(array_intersect($permissions, [$scope, $allAuthorization])) ? true : false;
        }
        return $this->accessTokenCan($scope);
    }

    private function accessTokenCan($scope)
    {
        return $this->accessToken ? $this->accessToken->can($scope) : false;;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $scopes
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = [])
    {
        return Container::getInstance()->make(PersonalAccessTokenFactory::class)->make(
            $this->authIdentifier(),
            $name,
            $scopes
        );
    }

    /**
     * Set the current access token for the user.
     *
     * @param  \Laravel\Passport\Token  $accessToken
     * @return $this
     */
    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return Illuminate\Contracts\Auth\Authenticatable
     */
    public function findForPassport($username)
    {
        $user = app(IUserModel::class)->fromAuthenticatable($this)->where('user_name', $username)->first();
        if ($user) {
            return $user->toAuthenticatable();
        }
        return null;
    }

    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param  string  $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return app()[\Drewlabs\Contracts\Hasher\IHasher::class]->check($password, $this->getAuthPassword());
    }
}
