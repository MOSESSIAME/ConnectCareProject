<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index()
    {
        $templates = Template::latest()->paginate(10);
        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ use the correct column: name (not title)
            'name'     => ['required', 'string', 'max:150', Rule::unique('templates', 'name')],
            'body'     => ['required', 'string'],
            'channel'  => ['required', Rule::in('sms', 'email', 'whatsapp')],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $validated['created_by'] = Auth::id();

        Template::create($validated);

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    /**
     * Show a single template.
     */
    public function show(Template $template)
    {
        return view('templates.show', compact('template'));
    }

    /**
     * Show the form for editing a template.
     */
    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            // ✅ unique on name; ignore this template's id
            'name'     => ['required', 'string', 'max:150', Rule::unique('templates', 'name')->ignore($template->id)],
            'body'     => ['required', 'string'],
            'channel'  => ['required', Rule::in('sms', 'email', 'whatsapp')],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $template->update($validated);

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()
            ->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
