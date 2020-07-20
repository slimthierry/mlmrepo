<?php


namespace Drewlabs\Contracts\Notification;

interface Notifiable
{
    /**
     * Returns the content|notification receiver unique identifier. It can be either an email address, a phonenumber, or device unique identifier
     *
     * @return INotificationReceiver[]
     */
    public function getNotificationReceivers();

    /**
     * Returns the identifier of the notification being sent
     *
     * @return INotificationSender
     */
    public function getNotificationSender();

    /**
     * Return the notification server configuration object
     *
     * @return INotificationServer
     */
    public function notificationServerConfigs();


    /**
     * Add a new receiver to the list of receivers
     *
     * @param INotificationReceiver $receiver
     * @return static
     */
    public function pushNotificationReceiver(INotificationReceiver $receiver);

    /**
     * Set the details of the notification sender
     *
     * @param INotificationSender $sender
     * @return static
     */
    public function setNotificationSender(INotificationSender $sender);

}
