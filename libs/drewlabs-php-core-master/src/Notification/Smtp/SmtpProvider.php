<?php

namespace Drewlabs\Core\Notification\Smtp;

use Drewlabs\Contracts\Notification\IMailNotifier;
use Drewlabs\Contracts\Notification\Notifiable;

class SmtpProvider implements IMailNotifier
{
    /**
     * @var Notifiable
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


    private function sendMail($receiver, $content, array $attachedReceivers, $subject, $rawContent = null)
    {
        try {
            // Creates and configure a Symfony Mail transprt instance
            $transport = (new \Swift_SmtpTransport(
                $this->notifiable->notificationServerConfigs()->getServerHost(),
                $this->notifiable->notificationServerConfigs()->getServerPort()
            ))
                ->setUsername($this->notifiable->notificationServerConfigs()->getClientUniqueIdentifier())
                ->setPassword($this->notifiable->notificationServerConfigs()->getClientSecret());
            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);
            // Create and email message
            $message = (new \Swift_Message($subject))
                ->setFrom($this->notifiable->getNotificationSender()->getUniqueIdentifier(), $this->notifiable->getNotificationSender()->getUniqueIdentifier())
                ->setTo($receiver)
                ->setBody($content, "text/html");
            foreach ($attachedReceivers as $value) {
                # code...
                $message = $message->addBcc($value);
            }
            $response = $mailer->send($message);
            return $response;
        } catch (\Exception $e) {
            // Write a better log handler later
            throw new \RuntimeException($e->getMessage());
        }
    }
}
