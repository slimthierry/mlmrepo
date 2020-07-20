<?php

namespace Drewlabs\Core\Jwt;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Contracts\Http\IRequest;
use Drewlabs\Contracts\Jwt\IPayloadVerifier;
use Drewlabs\Contracts\Jwt\ITokenManager;
use Drewlabs\Core\Jwt\Payload\ClaimTypes;

class JwtAuth
{

    /**
     * JWT Token management class
     *
     * @var ITokenManager
     */
    protected $manager;

    /**
     * A wrapper arround php frameworks or Libraries request
     *
     * @var IRequest
     */
    protected $request;

    /**
     * @var IPayloadVerifier
     */
    protected $verifier;

    /**
     * An IAuthenticatableProvider entity
     *
     * @var IAuthenticatableProvider
     */
    protected $provider;

    private $parser;

    public function __construct(
        ITokenManager $manager,
        IAuthenticatableProvider $provider,
        IRequest $request,
        IPayloadVerifier $verifier = null
    ) {
        $this->manager = $manager;
        $this->provider = $provider;
        $this->request = $request;
        $this->verifier = $verifier;
        $this->parser = new JwtRequestParser();
    }

    /**
     * Set an authenticatable user from a JWT token present in the HTTP header
     *
     * @param  Authenticatable $user
     * @param array  $custom_claims
     *
     * @return string A signed JWT
     *
     */
    public function fromUser(Authenticatable $user, $custom_claims = [ClaimTypes::OAUTH2_SCOPE => array('*')])
    {
        $payload = $this->makePayload($user->authIdentifier(), $custom_claims);
        return $this->manager->encodeToken($payload)->{'getToken'}();
    }

    /**
     * Generate an authentication token from an authentiicatable entity
     *
     * @param string  $token
     *
     * @return Authenticatable
     * @throws \RuntimeException
     */
    public function toUser($token)
    {
        $payload = $this->manager->decodeToken($token);
        if (isset($this->verifier) && ($this->verifier->verify($payload) === false)) {
            throw new \RuntimeException("Unauthorized access, untrusted token issuer");
        }
        return $this->provider->findById($payload[ClaimTypes::OAUTH2_SUBJECT]);
    }

    /**
     * Invalidate jwt token
     *
     * @param string  $token
     *
     * @return void
     *
     */
    public function invalidate($token)
    {
        return $this->manager->invalidateToken($token);
    }

    /**
     * Parse request and return jwt token
     *
     * @param string $method
     * @param string $header
     * @param string $query
     *
     * @throws \Drewlabs\Core\Jwt\Exceptions\TokenNotFoundException
     * @return string
     */
    public function parseToken($method = 'bearer', $header = 'authorization', $query = 'token')
    {
        return $this->parser->parse($this->request, $method, $header, $query);
    }

    /**
     * Set the request wrapper object
     *
     * @param IRequest $request
     *
     * @return static
     */
    public function setRequest(IRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set the request user provider
     *
     * @param IAuthenticatableProvider $user
     *
     * @return static
     */
    public function setAuthenticatableProvider(IAuthenticatableProvider $user)
    {
        $this->provider = $user;
        return $this;
    }

    /**
     * Get the token manager instance
     *
     * @return TokenManager
     */
    public function manager()
    {
        return $this->manager;
    }

    /**
     * Handler for generating a jwt payload based on user and custom claims
     *
     * @param mixed $subject
     * @param array $custom_claims
     * @return void
     */
    public function makePayload($subject, array $custom_claims = [])
    {
        return $this->manager->{'payloadFactory'}()->make(
            array_merge($custom_claims, array(ClaimTypes::OAUTH2_SUBJECT => $subject))
        )->resolve();
    }
}
