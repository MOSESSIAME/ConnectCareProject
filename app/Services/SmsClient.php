<?php

namespace App\Services;

use Twilio\Rest\Client;
use Exception;

class SmsClient
{
    protected Client $twilio;
    protected ?string $fromNumber;
    protected ?string $messagingServiceSid;

    public function __construct()
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $this->fromNumber          = config('services.twilio.from');
        $this->messagingServiceSid = config('services.twilio.messaging_service_sid');

        if (!$sid || !$token) {
            throw new Exception('Twilio credentials are missing.');
        }

        $this->twilio = new Client($sid, $token);
    }

    /**
     * Send an SMS and return the Message SID.
     */
    public function send(string $toE164, string $message): string
    {
        $payload = ['to' => $toE164, 'body' => $message];

        if ($this->messagingServiceSid) {
            $payload['messagingServiceSid'] = $this->messagingServiceSid;
        } elseif ($this->fromNumber) {
            $payload['from'] = $this->fromNumber;
        } else {
            throw new Exception('No FROM number or Messaging Service SID configured.');
        }

        $msg = $this->twilio->messages->create($payload['to'], $payload);

        return $msg->sid;
    }
}
