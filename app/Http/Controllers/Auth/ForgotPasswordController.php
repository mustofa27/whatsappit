<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No user found with this email address.',
        ]);

        try {
            // Generate unique token
            $token = Str::random(64);
            
            // Store token in database (usually password_resets table)
            DB::table('password_resets')->updateOrInsert(
                ['email' => $validated['email']],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            // Send reset email
            $user = User::where('email', $validated['email'])->first();
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $validated['email']]);

            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));

            Log::info('Password reset link sent', ['email' => $validated['email']]);

            return back()->with('success', 'Password reset link sent to your email. Please check your inbox.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset link', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? null,
            ]);

            return back()->with('error', 'Failed to send reset link. Please try again.');
        }
    }

    /**
     * Show reset password form
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Find password reset record
            $reset = DB::table('password_resets')
                ->where('email', $validated['email'])
                ->first();

            if (!$reset) {
                return back()->withInput()->with('error', 'Invalid password reset request.');
            }

            // Verify token
            if (!Hash::check($validated['token'], $reset->token)) {
                return back()->withInput()->with('error', 'Invalid or expired password reset link.');
            }

            // Check if link expired (valid for 1 hour)
            if (now()->diffInMinutes($reset->created_at) > 60) {
                DB::table('password_resets')->where('email', $validated['email'])->delete();
                return back()->with('error', 'Password reset link has expired. Please request a new one.');
            }

            // Update password
            $user = User::where('email', $validated['email'])->first();
            $user->update(['password' => Hash::make($validated['password'])]);

            // Delete reset token
            DB::table('password_resets')->where('email', $validated['email'])->delete();

            Log::info('Password reset successfully', ['email' => $validated['email']]);

            return redirect()->route('login')
                ->with('success', 'Password reset successful. You can now login with your new password.');
        } catch (\Exception $e) {
            Log::error('Failed to reset password', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? null,
            ]);

            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }
}
