<?php

namespace App\Services\Messaging;

use App\Models\Member;

interface ChannelContract
{
    /**
     * Send a message body to a single recipient (Member).
     * Should throw an exception on hard failure.
     */
    public function send(Member $recipient, string $body, string $subject = null): void;
}
