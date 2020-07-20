<?php

namespace Drewlabs\Core\Notification\AwsSES\Core;

class AwsSESMailableIdentityVerifierWrapper
{

    /**
     * Instance of Aws client interface
     *
     * @var \Aws\AwsClientInterface|\Aws\AwsClient|\Aws\Ses\SesClient
     */
    private $client;

    public function __construct(\Aws\AwsClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * This utility method provide with an interface for verifying email identiy that will be used for sending emails
     *
     * @param string $email
     * @return \Aws\Result
     */
    public function verifyEmailIdentity($email)
    {
        try {
            $result = $this->client->verifyEmailIdentity([
                'EmailAddress' => $email,
            ]);
            return $result;
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Amazon SES can send email only from verified email addresses or domains.
     * By verifying an email address, you demonstrate that you’re the owner of
     * that address and want to allow Amazon SES to send email from that address.
     * This method allow creating a verified domain on one aws account
     *
     * @param string $domain
     * @return \Aws\Result
     */
    public function verifyDomainIdentity($domain)
    {
        try {
            $result = $this->client->verifyDomainIdentity([
                'Domain' => $domain,
            ]);
            return $result;
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * Use this method to  retrieve a list of email addresses submitted
     * in the current AWS Region, regardless of verification status,
     *
     * @return void
     */
    public function listEmailAddresses()
    {
        try {
            $result = $this->client->listIdentities([
                'IdentityType' => 'Domain',
            ]);
            var_dump($result);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * This method allows to retrieve a list of email domains or an email
     * submitted in the current AWS Region, regardless of verification status.
     *
     * @param string $identity
     * @return void
     */
    public function deleteIdentity($identity)
    {
        try {
            $result = $this->client->deleteIdentity([
                'Identity' => $identity,
            ]);
            var_dump($result);
        } catch (\Aws\Exception\AwsException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
