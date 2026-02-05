<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #007bff;">Password Reset Request</h2>
    </div>

    <p>Hi {{ $user->name }},</p>

    <p>We received a request to reset the password for your WAIt account. Click the button below to reset your password.</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Reset Password
        </a>
    </div>

    <p style="color: #666; font-size: 14px;">Or copy and paste this link in your browser:</p>
    <p style="color: #0066cc; word-break: break-all; font-size: 12px;">{{ $resetUrl }}</p>

    <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

    <p style="color: #666; font-size: 12px;">
        <strong>Important:</strong> This link will expire in 1 hour.
    </p>

    <p style="color: #666; font-size: 12px;">
        If you didn't request a password reset, please ignore this email or contact support if you have questions.
    </p>

    <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

    <p style="color: #999; font-size: 12px; margin: 0;">
        WAIt - WhatsApp Integration Tool<br>
        © 2026 All rights reserved
    </p>
</div>
