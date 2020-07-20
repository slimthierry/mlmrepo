<?php

namespace Drewlabs\Core\Notification\Sendgrid;

use Drewlabs\Contracts\Notification\IMailNotifier;
use Drewlabs\Contracts\Notification\Notifiable;

class SendGridProvider implements IMailNotifier
{

    /**
     * @Notifiable
     */
    private $notifiable;

    public function __construct(Notifiable $notifiable = null)
    {
        $this->notifiable = $notifiable;
    }

    /**
     * @inheritDoc
     */
    public function setNotification($value)
    {
        $this->notifiable = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNotifiable()
    {
        return $this->notifiable;
    }


    /**
     * @inheritDoc
     */
    public function notify()
    {
        if (is_null($this->notifiable)) {
            throw new \RuntimeException('Error : Uninitialized notification object');
        }
        $result = [];
        if (is_array($this->notifiable->getNotificationReceivers())) {
            foreach ($this->notifiable->getNotificationReceivers() as $value) {
                # code...
                $result[] = $this->sendMail($value->getReceiver(), $value->getNotificationContent(), $value->getAttachedReceivers(), $value->getSubject());
            }
        }
        return $result;
    }


    /**
     * Undocumented function
     *
     * @param string $receiver
     * @param array $attachedReceivers
     * @return void
     */
    private function sendMail($receiver, $content, array $attachedReceivers, $subject, $rawContent = null)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($this->notifiable->getNotificationSender()->getUniqueIdentifier(), $this->notifiable->getNotificationSender()->getUniqueIdentifier());
        $email->setSubject($subject);
        $email->addTo($receiver);
        $email->addBccs($attachedReceivers);
        $email->addContent(
            "text/html",
            $content
        );
        $sendgrid = new \SendGrid($this->notifiable->notificationServerConfigs()->getClientSecret());
        try {
            $response = $sendgrid->send($email);
            return $response;
        } catch (\Exception $e) {
            // Write a better log handler later
            throw new \RuntimeException($e->getMessage());
        }
    }
}
