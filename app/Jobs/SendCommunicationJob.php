<?php

namespace App\Jobs;

use App\Models\Communication;
use App\Models\Member;
use App\Services\SmsClient; // ⬅️ using our Twilio client
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCommunicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Communication $communication) {}

    public function handle(): void
    {
        $comm   = $this->communication->fresh();
        $body   = $comm->body;
        $title  = $comm->title;
        $channel = strtolower($comm->channel ?? 'sms'); // 'sms' | 'email' | 'whatsapp' ...

        $query = $this->buildAudienceQuery($comm->audience, $comm->filters ?? []);

        $sentAny = false;
        $failures = 0;

        $query->chunkById(500, function ($chunk) use ($channel, $body, $title, &$sentAny, &$failures) {
            foreach ($chunk as $member) {
                try {
                    $personalizedBody = $this->personalize($body, $member);
                    $this->sendViaChannel($channel, $member, $personalizedBody, $title);
                    $sentAny = true;
                } catch (\Throwable $e) {
                    $failures++;
                    Log::warning('Message send failed: '.$e->getMessage(), [
                        'member_id' => $member->id,
                        'channel'   => $channel,
                    ]);
                }
            }
        });

        $this->communication->update([
            'status'  => $failures > 0 && $sentAny ? 'sent' : ($sentAny ? 'sent' : 'failed'),
            'sent_at' => now(),
        ]);
    }

    private function buildAudienceQuery(string $audience, array $filters): Builder
    {
        /** @var Builder $q */
        $q = Member::query();

        return match ($audience) {
            'all'           => $q,
            'members'       => $q->where('type', 'Member'),
            'first_timers'  => $q->where('type', 'First-timer'),
            'new_converts'  => $q->where('type', 'New Convert'),
            // 'by_zone'       => $q->where('zone_id', $filters['zone_id'] ?? 0),
            // 'by_district'   => $q->where('district_id', $filters['district_id'] ?? 0),
            // 'by_homecell'   => $q->where('homecell_id', $filters['homecell_id'] ?? 0),
            default         => $q,
        };
    }

    private function personalize(string $body, Member $m): string
    {
        // simple tags: {name}
        return strtr($body, [
            '{name}' => $m->name ?? $m->full_name ?? 'Friend',
        ]);
    }

    /**
     * Route by channel. Only SMS is implemented now (Twilio).
     * Email/WhatsApp are left as placeholders for later.
     */
    private function sendViaChannel(string $channel, Member $member, string $body, ?string $title = null): void
    {
        switch ($channel) {
            case 'sms':
                $to = $this->normalizeToE164Zambia($member->phone ?? null);
                if (!$to) {
                    throw new \RuntimeException('Member has no valid phone.');
                }
                $client = new SmsClient();
                // SmsClient::send(string $to, string $message): string (returns Twilio SID)
                $sid = $client->send($to, $body);
                Log::info('SMS queued/sent', ['to' => $to, 'sid' => $sid]);
                break;

            case 'email':
                // TODO: integrate your mailer here (subject = $title, body = $body)
                // Mail::to($member->email)->send(new YourMailable($title, $body));
                break;

            case 'whatsapp':
                // TODO: implement WhatsApp (Cloud API / vendor of choice)
                break;

            default:
                throw new \InvalidArgumentException("Unsupported channel: {$channel}");
        }
    }

    /**
     * Best-effort normalizer for Zambian MSISDNs → E.164.
     * Examples:
     *   0978123456  → +260978123456
     *   978123456   → +260978123456
     *   +260978123456 stays as-is
     */
    private function normalizeToE164Zambia(?string $raw): ?string
    {
        if (!$raw) return null;
        $digits = preg_replace('/\D+/', '', $raw);

        // Already E.164 with +260...
        if (str_starts_with($raw, '+260')) {
            return $raw;
        }

        // If it started with 0 and is 10 digits (e.g., 097..., 095..., 096...)
        if (strlen($digits) === 10 && $digits[0] === '0') {
            return '+260' . substr($digits, 1);
        }

        // If it's 9 digits (already without leading 0)
        if (strlen($digits) === 9) {
            return '+260' . $digits;
        }

        // Fallback: if it already looks like +<country><number>
        if (preg_match('/^\+?[1-9]\d{6,15}$/', $raw)) {
            return str_starts_with($raw, '+') ? $raw : '+' . $raw;
        }

        return null;
    }
}
