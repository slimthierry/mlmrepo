<?php

namespace Drewlabs\Packages\Identity\Contracts;

interface IUserNotificationActionsHandler
{
    /**
     * Send a notification when user is created
     *
     * @param boolean $requestHasPassword
     * @param array $params
     * @return void
     */
    public function sendOnCreateUser($requestHasPassword, array $params);
}
