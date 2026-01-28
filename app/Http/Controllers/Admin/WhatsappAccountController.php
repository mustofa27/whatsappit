<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsappAccountController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
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
        $validated['status'] = 'disconnected';

        $account = WhatsappAccount::create($validated);

        // Redirect to connect page to scan QR code
        return redirect()->route('admin.accounts.connect', $account)
            ->with('success', 'WhatsApp account created! Please scan the QR code to connect.');
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
        // Delete Evolution API instance before deleting account
        try {
            $this->whatsappService->deleteInstance($account);
        } catch (\Exception $e) {
            // Log but don't prevent deletion
        }

        $account->delete();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'WhatsApp account deleted successfully!');
    }

    public function initialize(Request $request, WhatsappAccount $account)
    {
        try {
            $result = $this->whatsappService->initialize($account);
            
            return redirect()->route('admin.accounts.connect', $account)
                ->with('success', 'Initialization started. Please scan the QR code with WhatsApp.');
        } catch (\Exception $e) {
            return redirect()->route('admin.accounts.show', $account)
                ->with('error', 'Failed to initialize: ' . $e->getMessage());
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

    public function connect(WhatsappAccount $account)
    {
        try {
            // Initialize Evolution API instance and get QR code
            $result = $this->whatsappService->initialize($account);
            $qrCode = $result['qrcode'] ?? null;
            
            if (!$qrCode) {
                // Try to get QR code separately
                $qrCode = $this->whatsappService->getQRCode($account);
            }
            
            return view('admin.accounts.connect-new', compact('account', 'qrCode'));
        } catch (\Exception $e) {
            return redirect()->route('admin.accounts.show', $account)
                ->with('error', 'Failed to generate QR code: ' . $e->getMessage());
        }
    }

    public function checkStatus(WhatsappAccount $account)
    {
        try {
            // Check status via Evolution API
            $status = $this->whatsappService->checkStatus($account);
            
            // Refresh account from database
            $account->refresh();
            
            return response()->json([
                'status' => $account->status,
                'connected' => $status['connected'],
                'qr_code' => $account->qr_code,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'connected' => false,
                'error' => $e->getMessage(),
            ]);
        }
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
