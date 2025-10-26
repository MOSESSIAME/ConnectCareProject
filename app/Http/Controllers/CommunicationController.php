<?php

namespace App\Http\Controllers;

use App\Jobs\SendCommunicationJob;
use App\Models\Communication;
use App\Models\Template;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CommunicationController extends Controller
{
    /**
     * List all communications.
     */
    public function index()
    {
        $records = Communication::with('creator')
            ->latest()
            ->paginate(15);

        // <-- renders resources/views/communications/index.blade.php
        return view('communications.index', compact('records'));
    }

    /**
     * Show the COMPOSE form (not template form).
     */
    public function create(Request $request)
    {
        // Active templates to quickly prefill body/subject
        $templates = Template::where('is_active', true)
            ->orderBy('name')
            ->get(['id','name','channel','subject','body']);

        // Build a resilient "display_name" for members (schema-safe)
        $q = Member::query()->select('id', 'type');

        if (Schema::hasColumn('members', 'name')) {
            $q->addSelect(DB::raw("name as display_name"))->orderBy('name');
        } elseif (Schema::hasColumn('members', 'full_name')) {
            $q->addSelect(DB::raw("full_name as display_name"))->orderBy('full_name');
        } elseif (Schema::hasColumn('members', 'first_name') && Schema::hasColumn('members', 'last_name')) {
            $q->addSelect(DB::raw("CONCAT(first_name, ' ', last_name) as display_name"))
              ->orderBy('first_name')->orderBy('last_name');
        } else {
            $fallback = "COALESCE(NULLIF(username,''), NULLIF(email,''), NULLIF(phone,''), CONCAT('Member #', id))";
            $q->addSelect(DB::raw("$fallback as display_name"))->orderBy('id');
        }

        // Optional quick search (?q=)
        if ($term = trim((string) $request->query('q'))) {
            $q->where(function ($qq) use ($term) {
                foreach (['name','full_name','first_name','last_name','username','email','phone'] as $col) {
                    if (Schema::hasColumn('members', $col)) {
                        $qq->orWhere($col, 'like', "%{$term}%");
                    }
                }
                if (is_numeric($term)) {
                    $qq->orWhere('id', (int) $term);
                }
            });
        }

        $members = $q->limit(500)->get();

        // <-- renders resources/views/communications/create.blade.php
        return view('communications.create', compact('templates','members'));
    }

    /**
     * Store & queue/schedule a communication.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => ['required','string','max:120'],
            'channel'         => ['required', Rule::in(['sms','whatsapp','email'])],
            'audience'        => ['required', Rule::in(['all','members','first_timers','new_converts','single'])],
            'member_id'       => ['nullable','exists:members,id','required_if:audience,single'],
            'template_id'     => ['nullable','exists:templates,id'],
            'subject'         => ['nullable','string','max:150'],
            'body'            => ['nullable','string','max:5000'], // required if no template selected
            'filters'         => ['nullable','array'],               // e.g. ['from_date' => 'YYYY-MM-DD','to_date' => 'YYYY-MM-DD']
            'filters.from_date' => ['nullable','date'],
            'filters.to_date'   => ['nullable','date','after_or_equal:filters.from_date'],
            'scheduled_at'     => ['nullable','date'],
        ]);

        // If a template is picked, use its body/subject (and channel if you want).
        if (!empty($data['template_id'])) {
            $tpl = Template::findOrFail($data['template_id']);
            $data['body']    = $data['body'] ?: $tpl->body;
            $data['subject'] = $data['subject'] ?? $tpl->subject;
            // If you want channel to follow template strictly, uncomment:
            // $data['channel'] = $tpl->channel;
        }

        // Ensure we have a message body
        if (empty($data['body'])) {
            return back()
                ->withErrors(['body' => 'Please provide a message body or select a template.'])
                ->withInput();
        }

        // Normalize scheduled_at (from datetime-local)
        $scheduledAt = !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null;

        $communication = Communication::create([
            'title'        => $data['title'],
            'channel'      => $data['channel'],
            'audience'     => $data['audience'],
            'member_id'    => $data['member_id'] ?? null,
            'template_id'  => $data['template_id'] ?? null,
            'subject'      => $data['subject'] ?? null,
            'body'         => ($data['channel'] === 'sms')
                                ? trim(preg_replace('/\s+/', ' ', $data['body'])) // keep SMS concise
                                : $data['body'],
            'filters'      => !empty($data['filters']) ? json_encode($data['filters']) : null,
            'status'       => 'queued',
            'scheduled_at' => $scheduledAt,
            'created_by'   => Auth::id(),
        ]);

        // Queue now or schedule for later
        if ($scheduledAt && now()->lt($scheduledAt)) {
            SendCommunicationJob::dispatch($communication)->delay($scheduledAt);
        } else {
            SendCommunicationJob::dispatch($communication);
        }

        return redirect()
            ->route('communications.index')
            ->with('success', 'Message queued successfully for delivery.');
    }

    /**
     * Show a single communication.
     */
    public function show(Communication $communication)
    {
        return view('communications.show', ['item' => $communication]);
    }
}
