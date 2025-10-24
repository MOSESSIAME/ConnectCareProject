<?php

namespace App\Services\Messaging\Channels;

use App\Models\Member;
use App\Services\Messaging\ChannelContract;
use Illuminate\Support\Facades\Log;

class SmsChannel implements ChannelContract
{
    public function send(Member $recipient, string $body, string $subject = null): void
    {
        $to = $recipient->phone;

        if (!$to) {
            throw new \RuntimeException('Recipient has no phone number.');
        }

        // TODO: Integrate Airtel/MTN/Zamtel SDK or HTTP API here.
        // Example (pseudo):
        // app(SmsGateway::class)->send($to, $body);

        Log::info("[SMS] to: {$to} | body: {$body}");
    }
}
