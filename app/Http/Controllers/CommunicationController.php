<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->paginate(15);
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // NOTE: use `name` (NOT title)
            'name'    => ['required', 'string', 'max:150', Rule::unique('templates', 'name')],
            'channel' => ['required', Rule::in(['sms', 'whatsapp', 'email'])],
            'subject' => ['nullable', 'string', 'max:150'], // ignored for sms
            'body'    => ['required', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // For SMS we ignore subject; for others keep what was typed
        if ($data['channel'] === 'sms') {
            $data['subject'] = null;
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        Template::create($data);

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            // NOTE: unique rule references templates.name
            'name'    => ['required', 'string', 'max:150', Rule::unique('templates', 'name')->ignore($template->id)],
            'channel' => ['required', Rule::in(['sms', 'whatsapp', 'email'])],
            'subject' => ['nullable', 'string', 'max:150'],
            'body'    => ['required', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($data['channel'] === 'sms') {
            $data['subject'] = null;
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $template->update($data);

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted.');
    }
}
