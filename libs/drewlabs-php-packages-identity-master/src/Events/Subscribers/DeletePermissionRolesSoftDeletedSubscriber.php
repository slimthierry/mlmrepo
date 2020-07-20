<?php //

namespace  Drewlabs\Packages\Identity\Events\Subscribers;

use Drewlabs\Core\Observable\SubscriberProvider as Subscriber;

final class DeletePermissionRolesSoftDeletedSubscriber extends Subscriber
{
    /**
     * Respond to an Event call
     *
     * @param mixed $credentials
     * @return void
     */
    public function listen($data = null)
    {
        (new \Drewlabs\Packages\Identity\PermissionRole())->whereNotNull('deleted_at')->forceDelete();
    }
}
