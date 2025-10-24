<?php

namespace App\Services\Messaging\Channels;

use App\Models\Member;
use App\Services\Messaging\ChannelContract;
use Illuminate\Support\Facades\Mail;

class EmailChannel implements ChannelContract
{
    public function send(Member $recipient, string $body, string $subject = null): void
    {
        $to = $recipient->email;

        if (!$to) {
            throw new \RuntimeException('Recipient has no email.');
        }

        Mail::raw($body, function ($m) use ($to, $subject) {
            $m->to($to)->subject($subject ?: 'Message');
        });
    }
}
