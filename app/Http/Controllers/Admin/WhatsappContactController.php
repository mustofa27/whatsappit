<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappAccount;
use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;

class WhatsappContactController extends Controller
{
    public function index(Request $request)
    {
        $query = WhatsappContact::with('whatsappAccount');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->input('tag'));
        }

        if ($request->filled('account_id')) {
            $query->where('whatsapp_account_id', $request->input('account_id'));
        }

        $contacts = $query->orderBy('name')->paginate(20)->appends($request->query());
        $accounts = WhatsappAccount::all();

        return view('admin.contacts.index-new', compact('contacts', 'accounts'));
    }

    public function create()
    {
        $accounts = WhatsappAccount::all();
        return view('admin.contacts.create-new', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'contact_number' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $validated['tags'] = $this->normalizeTags($validated['tags'] ?? null);

        WhatsappContact::create($validated);

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact created successfully');
    }

    public function show(WhatsappContact $contact)
    {
        $contact->load('whatsappAccount');

        $messages = WhatsappMessage::where('whatsapp_account_id', $contact->whatsapp_account_id)
            ->where('contact_number', $contact->contact_number)
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.contacts.show-new', compact('contact', 'messages'));
    }

    public function edit(WhatsappContact $contact)
    {
        $accounts = WhatsappAccount::all();
        return view('admin.contacts.edit-new', compact('contact', 'accounts'));
    }

    public function update(Request $request, WhatsappContact $contact)
    {
        $validated = $request->validate([
            'whatsapp_account_id' => 'required|exists:whatsapp_accounts,id',
            'contact_number' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $validated['tags'] = $this->normalizeTags($validated['tags'] ?? null);

        $contact->update($validated);

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', 'Contact updated successfully');
    }

    public function destroy(WhatsappContact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully');
    }

    private function normalizeTags(?string $tags): ?array
    {
        if (!$tags) {
            return null;
        }

        $items = collect(explode(',', $tags))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        return $items ?: null;
    }
}
