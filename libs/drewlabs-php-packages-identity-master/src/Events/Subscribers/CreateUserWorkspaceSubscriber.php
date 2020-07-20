<?php

namespace  Drewlabs\Packages\Identity\Events\Subscribers;

use Drewlabs\Core\Observable\SubscriberProvider as Subscriber;
use Drewlabs\Packages\Identity\Helpers;
use Drewlabs\Packages\Workspace\Services\UserWorkspaceManager;

final class CreateUserWorkspaceSubscriber extends Subscriber
{

    /**
     *
     * @var UserWorkspaceManager
     */
    private $provider;

    public function __construct(UserWorkspaceManager $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritDoc
     */
    public function listen($data = null)
    {
        $workspaces = isset($data['workspaces']) ? $data['workspaces'] : null;
        $user = isset($data['user']) ? $data['user'] : null;
        if (isset($workspaces) && is_array($workspaces) && isset($user) && ($user instanceof \Drewlabs\Contracts\Auth\IUserModel)) {
            $user_id = Helpers::getUserIdFromAuthenticatable($user->toAuthenticatable(), 'drewlabs_workspace.models.user.class');
            $values = collect($workspaces)->map(function ($w) use ($user_id) {
                $status = isset($w['status']) ? $w['status'] : 1;
                $attributes = array_merge(
                    $w,
                    [
                        'user_id' => $user_id,
                        'status' => $status,
                        'notification_enabled' => 1,
                        'dashboard_module_id' => isset($w['dashboard_module_id']) ? $w['dashboard_module_id'] : null,
                    ]
                );
                $user_ws = $this->provider->create(
                    $attributes,
                    new \Drewlabs\Core\Data\DataProviderCreateHandlerParams([
                        'method' => 'insert',
                        'upsert' => true,
                        'upsert_conditions' => array(
                            'workspace_id' => $w['workspace_id'],
                            'user_id' => $user_id,
                        )
                    ])
                );
                return $user_ws->getKey();
            });
            if (isset($data['action']) && (strtoupper($data['action']) === 'USER_UPDATE')) {
                // Delete the workspaces that belongs to the current user and that are not in the list of created user_workspaces
                $this->provider->delete(array(
                    'where' => array(array('user_id', $user_id)),
                    'whereNotIn' => array('id', $values->toArray())
                ));
            }
        }
    }
}
