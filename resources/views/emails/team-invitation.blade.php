@component('mail::message')
# You're Invited to Join a Team

Hi {{ $teamMember->user->name }},

{{ $inviter->name }} has invited you to join their team on **WhatsAppIt** as a **{{ ucfirst($teamMember->role) }}**.

## What is WhatsAppIt?
WhatsAppIt is a powerful WhatsApp Business management platform that helps teams collaborate on customer communications.

## Your Role
As a **{{ ucfirst($teamMember->role) }}**:
@if($teamMember->role === 'admin')
- Full access to all features
- Manage team members and settings
- Create and manage WhatsApp accounts
- View analytics and reports
@elseif($teamMember->role === 'operator')
- Manage WhatsApp accounts
- Send and receive messages
- Manage conversations
- Create message templates
@else
- View reports and analytics
- Read-only access to all features
@endif

## Accept or Reject?

@component('mail::button', ['url' => $acceptUrl, 'color' => 'success'])
Accept Invitation
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'error'])
Reject Invitation
@endcomponent

**Note:** This invitation will expire in 7 days.

---

If you didn't expect this invitation, you can safely ignore this email.

Thanks,<br>
WhatsAppIt Team
@endcomponent
