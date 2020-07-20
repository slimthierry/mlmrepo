<?php

namespace Drewlabs\Core\Jwt\Payload;

final class ClaimTypes
{
    /**
     * Payload issuer http ressource which is unique for each platform issuing the jwt token
     */
    const OAUTH2_ISSUER = "iss";
    /**
     * Payload issuer https ressource which is unique for each platform issuing the jwt token
     */
    const OAUTH2_ISSUER_SSL = "isshttps";
    /**
     * Timestamp representation of the moment the token was issue or created
     */
    const OAUTH2_ISSUE_AT = "iat";
    /**
     * Token expiration date. Must be after the "iat" for a valid token
     */
    const OAUTH2_EXPIRATION = "exp";
    /**
     * Uniquer encode string used for blacklisting
     */
    const OAUTH2_JIT = "jti";
    /**
     * Token should not be used before the corresponding time
     */
    const OAUTH2_NOT_BEFORE = "nbf";

    /**
     * The subject unique identifier claim
     */
    const OAUTH2_SUBJECT = "sub";

    /**
     * Custom claim that reference unique identifier on an entity
     */
    const ENTITY_IDENTIFIER = "identifier";

    /**
     * Custom claim for holding date of birth of an entity or a user
     */
    const DATE_OF_BIRTH = "dob";

    /**
     * Custom claim for holding application permissions scope
     */
    const OAUTH2_SCOPE = "scopes";
}
