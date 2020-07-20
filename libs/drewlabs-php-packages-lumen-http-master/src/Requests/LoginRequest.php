<?php

namespace Drewlabs\Packages\Http\Requests;



class LoginRequest implements \Drewlabs\Core\Validator\Contracts\Validatable
{
    /**
     * {@inheritDoc}
     * Validate an incoming Login Request inputs
     */
    public function rules()
    {
        return array(
            'username' => 'required',
            'password' => 'required',
            'remember_me' => 'sometimes|boolean'
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
