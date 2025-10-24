<?php

namespace App\Jobs;

use App\Models\Communication;
use App\Models\Member;
use App\Services\Messaging\MessageSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendCommunicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Communication $communication) {}

    public function handle(): void
    {
        $comm  = $this->communication->fresh();
        $body  = $comm->body;
        $title = $comm->title;
        $sender = MessageSender::for($comm->channel);

        $query = $this->buildAudienceQuery($comm->audience, $comm->filters ?? []);

        $sentAny = false;
        $failures = 0;

        $query->chunkById(500, function ($chunk) use ($sender, $body, $title, &$sentAny, &$failures) {
            foreach ($chunk as $member) {
                try {
                    $personalizedBody = $this->personalize($body, $member);
                    $sender->send($member, $personalizedBody, $title);
                    $sentAny = true;
                } catch (\Throwable $e) {
                    $failures++;
                    Log::warning('Message send failed: '.$e->getMessage(), ['member_id' => $member->id]);
                }
            }
        });

        $comm->update([
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
            'by_zone'       => $q->where('zone_id', $filters['zone_id'] ?? 0),
            'by_district'   => $q->where('district_id', $filters['district_id'] ?? 0),
            'by_homecell'   => $q->where('homecell_id', $filters['homecell_id'] ?? 0),
            default         => $q,
        };
    }

    private function personalize(string $body, Member $m): string
    {
        // simple tags: {name}
        return strtr($body, [
            '{name}' => $m->name ?? 'Friend',
        ]);
    }
}
