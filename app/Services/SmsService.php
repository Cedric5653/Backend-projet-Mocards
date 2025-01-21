<?php

namespace App\Services;

use Twilio\Rest\Client;

class SMSService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            config('notifications.channels.sms.account_sid'),
            config('notifications.channels.sms.auth_token')
        );
    }

    public function send($to, $message)
    {
        return $this->client->messages->create($to, [
            'from' => config('notifications.channels.sms.from'),
            'body' => $message
        ]);
    }
}