<?php

namespace App\Services\Messaging\Channels;

use App\Models\Member;
use App\Services\Messaging\ChannelContract;
use Illuminate\Support\Facades\Log;

class WhatsappChannel implements ChannelContract
{
    public function send(Member $recipient, string $body, string $subject = null): void
    {
        $to = $recipient->phone; // or a dedicated whatsapp_number column

        if (!$to) {
            throw new \RuntimeException('Recipient has no WhatsApp number.');
        }

        // TODO: Integrate your WhatsApp provider (Cloud API / BSP).
        Log::info("[WHATSAPP] to: {$to} | body: {$body}");
    }
}
