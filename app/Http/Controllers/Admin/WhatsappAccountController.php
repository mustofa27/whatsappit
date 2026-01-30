<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Services\MetaWhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsappAccountController extends Controller
{
    protected $whatsappService;

    public function __construct(MetaWhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $accounts = WhatsappAccount::with('user')->latest()->paginate(20);
        return view('admin.accounts.index-new', compact('accounts'));
    }

    public function create()
    {
        return view('admin.accounts.create-new');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|unique:whatsapp_accounts,phone_number',
            'name' => 'required|string|max:255',
        ]);

        // Auto-set user_id from logged in user
        $validated['user_id'] = auth()->id();
        
        // Generate sender_key and sender_secret
        $validated['sender_key'] = 'sk_' . Str::random(32);
        $validated['sender_secret'] = 'ss_' . Str::random(40);
        $validated['status'] = 'pending';
        $validated['provider'] = 'meta';
        
        // Set default Meta credentials from config if available
        $validated['phone_number_id'] = config('services.meta_whatsapp.default_phone_number_id');
        $validated['waba_id'] = config('services.meta_whatsapp.default_waba_id');
        $validated['access_token'] = config('services.meta_whatsapp.default_access_token');

        $account = WhatsappAccount::create($validated);

        // Redirect to verify page
        return redirect()->route('admin.accounts.verify', $account)
            ->with('success', 'WhatsApp account created! Please verify your phone number.');
    }

    public function show(WhatsappAccount $account)
    {
        $account->load('user', 'messages');
        return view('admin.accounts.show-new', compact('account'));
    }

    public function edit(WhatsappAccount $account)
    {
        return view('admin.accounts.edit-new', compact('account'));
    }

    public function update(Request $request, WhatsappAccount $account)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|unique:whatsapp_accounts,phone_number,' . $account->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:connected,disconnected,connecting',
        ]);

        $account->update($validated);

        return redirect()->route('admin.accounts.index')
            ->with('success', 'WhatsApp account updated successfully!');
    }

    public function destroy(WhatsappAccount $account)
    {
        try {
            // Disconnect from Meta
            $this->whatsappService->disconnect($account);
        } catch (\Exception $e) {
            // Log but don't prevent deletion
        }

        $account->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'WhatsApp account deleted successfully!');
    }

    public function verify(WhatsappAccount $account)
    {
        return view('admin.accounts.verify-meta', compact('account'));
    }

    public function requestCode(Request $request, WhatsappAccount $account)
    {
        try {
            $result = $this->whatsappService->requestVerificationCode($account);
            
            return redirect()->route('admin.accounts.verify', $account)
                ->with('success', 'Verification code sent to ' . $account->phone_number . '. Check your SMS.');
        } catch (\Exception $e) {
            return redirect()->route('admin.accounts.verify', $account)
                ->with('error', 'Failed to send verification code: ' . $e->getMessage());
        }
    }

    public function verifyCode(Request $request, WhatsappAccount $account)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        try {
            $this->whatsappService->verifyPhoneNumber($account, $request->code);
            
            return redirect()->route('admin.accounts.show', $account)
                ->with('success', 'Phone number verified successfully! Your account is ready to send messages.');
        } catch (\Exception $e) {
            return redirect()->route('admin.accounts.verify', $account)
                ->with('error', $e->getMessage());
        }
    }

    public function regenerateKeys(WhatsappAccount $account)
    {
        $account->update([
            'sender_key' => 'sk_' . Str::random(32),
            'sender_secret' => 'ss_' . Str::random(40),
        ]);

        return redirect()->route('admin.accounts.show', $account)
            ->with('success', 'API keys regenerated successfully!');
    }

    public function disconnect(WhatsappAccount $account)
    {
        try {
            $this->whatsappService->disconnect($account);
            
            return redirect()->route('admin.accounts.show', $account)
                ->with('success', 'WhatsApp account disconnected successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.accounts.show', $account)
                ->with('error', 'Failed to disconnect: ' . $e->getMessage());
        }
    }
}
