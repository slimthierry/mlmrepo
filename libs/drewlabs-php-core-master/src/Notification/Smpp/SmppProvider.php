<?php

namespace Drewlabs\Core\Notification\Smpp;

use Drewlabs\Core\Smpp\GsmEncoder;
use Drewlabs\Core\Smpp\SMPP;
use Drewlabs\Core\Smpp\SmppAddress;
use Drewlabs\Core\Smpp\SmppClient;
use Drewlabs\Core\Smpp\SocketTransport;
use Drewlabs\Contracts\Notification\ISmsNotifier;
use Drewlabs\Contracts\Notification\Notifiable;

class SmppProvider implements ISmsNotifier
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
                $result[] = $this->send($value->getReceiver(), $value->getNotificationContent());
            }
        }
        return $result;
    }


    public function send($receiver, $content)
    {
        // Write the smpp transport initialization code
        SocketTransport::$defaultDebug = true;
        SocketTransport::$forceIpv4 = true;
        $transport = new SocketTransport(array($this->notifiable->notificationServerConfigs()->getServerHost()), $this->notifiable->notificationServerConfigs()->getServerPort());
        $transport->setSendTimeout(10000);
        $transport->setRecvTimeout(10000);
        $smpp = new SmppClient($transport);
        SmppClient::$sms_null_terminate_octetstrings = false;
        // Activate binary hex-output of server interaction
        $smpp->debug = false;
        $transport->debug = false;
        $transport->open();
        $smpp->bindTransmitter($this->notifiable->notificationServerConfigs()->getClientUniqueIdentifier(), $this->notifiable->notificationServerConfigs()->getClientSecret());
        $encodedMessage = GsmEncoder::utf8_to_gsm0338($content);
        $from = new SmppAddress($this->notifiable->getNotificationSender()->getUniqueIdentifier(), SMPP::TON_ALPHANUMERIC);
        // Send message
        # code...
        $to = new SmppAddress($receiver, SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);
        try {
            // Send
            return $smpp->sendSMS($from, $to, $encodedMessage);
        } catch (\Exception $e) {
            // Write a better log handler later
            throw new \RuntimeException($e->getMessage());
        } finally {
            // Close connection
            $smpp->close();
        }
    }
}
