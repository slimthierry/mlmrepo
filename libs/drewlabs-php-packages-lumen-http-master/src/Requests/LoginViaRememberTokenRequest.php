<?php

namespace Drewlabs\Packages\Http\Requests;



class LoginViaRememberTokenRequest implements \Drewlabs\Core\Validator\Contracts\Validatable
{
    /**
     * {@inheritDoc}
     * Validate an incoming Login Request inputs
     */
    public function rules()
    {
        return array(
            'identifier' => 'required',
            'remember_token' => 'required'
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {

    }

    /**
     * {@inheritDoc}
     * Returns validation error when login request validation fails
     */
    public function messages()
    {
        return array();
    }
}
