<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin) {
                abort(403, 'Unauthorized. Admin access required.');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $setting->update(['value' => $value]);
                \Cache::forget("setting.{$key}");
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Mail::raw('This is a test email from WAIt. If you received this, your SMTP configuration is working correctly!', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                    ->subject('Test Email - WAIt SMTP Configuration');
            });

            Log::info('Test email sent successfully', [
                'to' => $validated['test_email'],
                'admin_id' => auth()->id(),
            ]);

            return back()->with('success', "Test email sent successfully to {$validated['test_email']}. Please check your inbox.");
        } catch (\Exception $e) {
            Log::error('Failed to send test email', [
                'error' => $e->getMessage(),
                'to' => $validated['test_email'] ?? null,
            ]);

            return back()->with('error', 'Failed to send test email. Error: ' . $e->getMessage());
        }
    }
}
