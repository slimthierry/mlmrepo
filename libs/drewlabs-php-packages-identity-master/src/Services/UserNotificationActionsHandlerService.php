<?php

namespace Drewlabs\Packages\Identity\Services;

use Drewlabs\Packages\Identity\Contracts\IUserNotificationActionsHandler;

class UserNotificationActionsHandlerService implements IUserNotificationActionsHandler
{
    /**
     * Send a notification when user is created
     *
     * @param boolean $requestHasPassword
     * @param array $params
     * @return void
     */
    public function sendOnCreateUser($requestHasPassword, array $params)
    {
        if (!isset($params['email']) || !isset($params['username']) ||  !isset($params['password'])) {
            return;
        }
        $notifyOnCreate = \config('drewlabs_identity.notify_on_create', false);
        $mailClass = \config('drewlabs_identity.models.mails.class', null);
        // Checks if configurations allow sending notification
        if (!($requestHasPassword) || (($notifyOnCreate) && isset($mailClass) && \Drewlabs\Utils\Str::contains($mailClass, ['\\', '\\\\']) === true)) {
            // Creating a new mail entry will trigger
            (new $mailClass)->resetScope()->setAttributes([
                'subject' => __('user_create_subject'),
                'from' => \bloc_component_config('MAIL_SENDER_NAME'),
                'to' => $params['email'],
                'content' => view('identity.create-user-mail', [
                    'email' => $params['email'],
                    'username' => $params['username'],
                    'password' => $params['password']
                ]),
            ])->add();
        }
    }
}
