<?php

namespace Drewlabs\Core\Notification\AwsSES\Core;

use Drewlabs\Contracts\Notification\IMailNotifier;
use Drewlabs\Contracts\Notification\Notifiable;
use Aws\Exception\AwsException;
use Drewlabs\Core\Notification\NotificationResult;

class AwsSESProvider implements IMailNotifier
{

    /**
     * @Notifiable
     */
    private $notifiable;

    /**
     * Instance of Aws client interface
     *
     * @var \Aws\AwsClientInterface|\Aws\AwsClient|\Aws\Ses\SesClient
     */
    private $client;

    public function __construct(\Aws\AwsClientInterface $client, Notifiable $notifiable = null)
    {
        $this->notifiable = $notifiable;
        $this->client = $client;
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
                $result[] = $this->sendMail(
                    $value->getReceiver(),
                    $value->getNotificationContent(),
                    $value->getAttachedReceivers(),
                    $value->getSubject()
                );
            }
        }
        return $result;
    }


    /**
     * Undocumented function
     *
     * @param string $receiver
     * @param array $attachedReceivers
     * @return NotificationResult
     */
    private function sendMail($receiver, $content, array $attachedReceivers, $subject, $rawContent = null)
    {
        $char_set = 'UTF-8';
        $rawContent = preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($content))));
        $recipients = array_values(array_filter((array_merge(array($receiver), $attachedReceivers)), function ($address) {
            return !empty(trim($address));
        }));
        try {
            $result = $this->client->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipients,
                ],
                'ReplyToAddresses' => [$this->notifiable->getNotificationSender()->getUniqueIdentifier()],
                'Source' => $this->notifiable->getNotificationSender()->getUniqueIdentifier(),
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => $char_set,
                            'Data' => $content,
                        ],
                        'Text' => [
                            'Charset' => $char_set,
                            'Data' => $rawContent,
                        ],
                    ],
                    'Subject' => [
                        'Charset' => $char_set,
                        'Data' => $subject,
                    ],
                ],
                // If you aren't using a configuration set, comment or delete the
                // following line
                'ConfigurationSetName' => null,
            ]);
            return $this->parseAwsResult($result)->copyWith(['recipients' => \implode(',', $recipients)]);
        } catch (AwsException $e) {
            return new NotificationResult([
                "status_code" => 500,
                'error' => "The email was not sent. Error message: " . $e->getAwsErrorMessage(),
                'date' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function parseAwsResult(\Aws\Result $result)
    {
        $metadata = null;
        $sendingDate = null;
        if ($result) {
            $metadata = $result->get('@metadata');
            $sendingDate = isset($metadata) && isset($metadata['headers']) && isset($metadata['headers']['date']) ?
                \DateTime::createFromFormat("D, j M Y H:i:s e", $metadata['headers']['date'])->format("Y-m-d H:i:s") : null;
        }
        return new NotificationResult([
            'message_id' => $result->get('MessageId'),
            'status_code' => is_array($metadata) && isset($metadata['statusCode']) ? $metadata['statusCode'] : 200,
            'date' => $sendingDate
        ]);
    }
}
