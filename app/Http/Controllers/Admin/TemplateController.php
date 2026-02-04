<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use App\Models\WhatsappAccount;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index(Request $request)
    {
        $query = MessageTemplate::with('whatsappAccount')
            ->whereHas('whatsappAccount', function($q) {
                $q->where('user_id', auth()->id());
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by account
        if ($request->filled('account_id')) {
            $query->where('whatsapp_account_id', $request->account_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search by name or content
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('body_content', 'like', '%' . $request->search . '%');
            });
        }

        $templates = $query->orderBy('created_at', 'desc')->paginate(20);
        $accounts = WhatsappAccount::where('user_id', auth()->id())->get();

        return view('admin.templates.index', compact('templates', 'accounts'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $accounts = WhatsappAccount::where('user_id', auth()->id())->get();
        return view('admin.templates.create', compact('accounts'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'name' => 'required|string|max:255|unique:message_templates,name',
            'category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
            'language' => 'required|string|max:10',
            'header_type' => 'nullable|in:TEXT,IMAGE,VIDEO,DOCUMENT',
            'header_content' => 'nullable|string',
            'body_content' => 'required|string',
            'footer_content' => 'nullable|string|max:60',
            'buttons' => 'nullable|json',
        ]);

        // Parse buttons if provided
        if ($request->filled('buttons')) {
            $validated['buttons'] = json_decode($request->buttons, true);
        }

        $template = MessageTemplate::create($validated);

        // Extract and store variables
        $template->variables = $template->extractVariables();
        $template->save();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified template.
     */
    public function show(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        $template->load('whatsappAccount');
        return view('admin.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        // Only draft templates can be edited
        if ($template->status !== 'draft') {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Only draft templates can be edited.');
        }

        $accounts = WhatsappAccount::where('user_id', auth()->id())->get();
        return view('admin.templates.edit', compact('template', 'accounts'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        // Only draft templates can be updated
        if ($template->status !== 'draft') {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Only draft templates can be updated.');
        }

        $validated = $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'name' => 'required|string|max:255|unique:message_templates,name,' . $template->id,
            'category' => 'required|in:MARKETING,UTILITY,AUTHENTICATION',
            'language' => 'required|string|max:10',
            'header_type' => 'nullable|in:TEXT,IMAGE,VIDEO,DOCUMENT',
            'header_content' => 'nullable|string',
            'body_content' => 'required|string',
            'footer_content' => 'nullable|string|max:60',
            'buttons' => 'nullable|json',
        ]);

        // Parse buttons if provided
        if ($request->filled('buttons')) {
            $validated['buttons'] = json_decode($request->buttons, true);
        }

        $template->update($validated);

        // Update variables
        $template->variables = $template->extractVariables();
        $template->save();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        // Only draft templates can be deleted
        if ($template->status !== 'draft') {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Only draft templates can be deleted.');
        }

        $template->delete();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Preview template with sample data.
     */
    public function preview(Request $request, MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        $sampleData = $request->input('variables', []);
        $preview = $template->getPreview($sampleData);

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'variables' => $template->variables,
        ]);
    }

    /**
     * Duplicate an existing template.
     */
    public function duplicate(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->status = 'draft';
        $newTemplate->meta_template_id = null;
        $newTemplate->usage_count = 0;
        $newTemplate->last_used_at = null;
        $newTemplate->save();

        return redirect()
            ->route('admin.templates.edit', $newTemplate)
            ->with('success', 'Template duplicated successfully.');
    }

    /**
     * Submit template for approval (change from draft to pending).
     */
    public function submit(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        if ($template->status !== 'draft') {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Only draft templates can be submitted for approval.');
        }

        try {
            // Submit template to Meta WhatsApp API
            $whatsappService = new \App\Services\MetaWhatsappService();
            $result = $whatsappService->submitTemplate($template, $template->whatsappAccount);

            // Update template with Meta response
            $template->update([
                'status' => 'pending',
                'meta_template_id' => $result['meta_template_id'],
            ]);

            return redirect()
                ->route('admin.templates.index')
                ->with('success', 'Template submitted to Meta WhatsApp for approval. Status: ' . $result['status']);

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Failed to submit template: ' . $e->getMessage());
        }
    }

    /**
     * Approve template (for admin/testing purposes).
     */
    public function approve(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        if ($template->status !== 'pending') {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Only pending templates can be approved.');
        }

        $template->update(['status' => 'approved']);

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template approved successfully.');
    }

    /**
     * Reject template (for admin/testing purposes).
     */
    public function reject(MessageTemplate $template)
    {
        // Ensure user owns this template
        if ($template->whatsappAccount->user_id !== auth()->id()) {
            abort(403);
        }

        if ($template->status !== 'pending') {
            return redirect()
                ->route('admin.templates.index')
                ->with('error', 'Only pending templates can be rejected.');
        }

        $template->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template rejected.');
    }
}
