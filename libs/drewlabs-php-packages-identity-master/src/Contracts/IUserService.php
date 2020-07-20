<?php

namespace Drewlabs\Packages\Identity\Contracts;

use Illuminate\Http\Request;
use Psr\Http\Message\RequestInterface;

interface IUserService
{
    /**
     * Used to set user extra information after it get created
     *
     * @param Request|RequestInterface $request
     * @param mixed $user_id
     * @param mixed $role_label
     * @return void
     */
    public function createUserExtraInformation($request, $user_id, $role_label);

    /**
     * Update user extra information
     *
     * @param Request|RequestInterface $request
     * @param mixed $user_id
     * @return void
     */
    public function updateUserExtraInformation($request, $user_id);
}
