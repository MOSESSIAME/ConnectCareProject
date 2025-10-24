<?php

namespace App\Services\Messaging;

use App\Services\Messaging\Channels\SmsChannel;
use App\Services\Messaging\Channels\WhatsappChannel;
use App\Services\Messaging\Channels\EmailChannel;

class MessageSender
{
    public static function for(string $channel): ChannelContract
    {
        return match ($channel) {
            'sms'      => new SmsChannel(),
            'whatsapp' => new WhatsappChannel(),
            'email'    => new EmailChannel(),
            default    => throw new \InvalidArgumentException("Unknown channel: {$channel}")
        };
    }
}
