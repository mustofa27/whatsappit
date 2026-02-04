<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TeamMemberController extends Controller
{
    /**
     * Show team members list
     */
    public function index()
    {
        $user = auth()->user();
        $activeMembers = $user->activeTeamMembers()->paginate(10);
        $pendingInvitations = $user->pendingInvitations()->paginate(10);

        return view('admin.team-members.index', [
            'activeMembers' => $activeMembers,
            'pendingInvitations' => $pendingInvitations,
            'maxMembers' => $user->getMaxTeamMembers(),
            'memberCount' => $user->getTeamMemberCount(),
            'remainingSlots' => $user->getRemainingTeamSlots(),
            'canAddMember' => $user->canAddTeamMember(),
        ]);
    }

    /**
     * Show invite form
     */
    public function create()
    {
        $user = auth()->user();
        
        if (!$user->canAddTeamMember()) {
            return back()->with('error', "You've reached your team member limit. Upgrade your subscription to add more members.");
        }

        return view('admin.team-members.invite', [
            'maxMembers' => $user->getMaxTeamMembers(),
            'memberCount' => $user->getTeamMemberCount(),
            'remainingSlots' => $user->getRemainingTeamSlots(),
        ]);
    }

    /**
     * Send invitation to new team member
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Check if can add more members
        if (!$user->canAddTeamMember()) {
            return back()->with('error', "You've reached your team member limit ({$user->getMaxTeamMembers()}). Upgrade your subscription to add more members.");
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,operator,viewer',
        ]);

        try {
            // Find user by email - must be already registered
            $invitedUser = User::where('email', $validated['email'])->first();
            
            if (!$invitedUser) {
                return back()->with('error', "User with email {$validated['email']} is not registered. They must create an account first.");
            }

            // Check if invitation already exists
            $existing = TeamMember::where('team_owner_id', $user->id)
                ->where('user_id', $invitedUser->id)
                ->first();

            if ($existing) {
                return back()->with('error', 'This user is already invited to your team.');
            }

            // Create team member invitation
            $inviteToken = Str::random(64);
            $teamMember = TeamMember::create([
                'team_owner_id' => $user->id,
                'user_id' => $invitedUser->id,
                'role' => $validated['role'],
                'status' => 'pending',
                'invite_token' => $inviteToken,
                'invite_expires_at' => now()->addDays(7),
            ]);

            // TODO: Send invitation email
            // Mail::send('emails.team-invite', [
            //     'user' => $user,
            //     'invitedUser' => $invitedUser,
            //     'inviteUrl' => route('team-members.accept', ['token' => $inviteToken]),
            // ], function ($mail) use ($invitedUser) {
            //     $mail->to($invitedUser->email);
            // });

            Log::info('Team member invited', [
                'team_owner_id' => $user->id,
                'invited_user_id' => $invitedUser->id,
                'role' => $validated['role'],
            ]);

            return back()->with('success', "Invitation sent to {$validated['email']}");
        } catch (\Exception $e) {
            Log::error('Failed to invite team member', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);

            return back()->with('error', 'Failed to send invitation. Please try again.');
        }
    }

    /**
     * Update team member role
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        // Check authorization
        if ($teamMember->team_owner_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,operator,viewer',
        ]);

        $teamMember->update($validated);

        return back()->with('success', 'Team member role updated successfully.');
    }

    /**
     * Remove team member
     */
    public function destroy(TeamMember $teamMember)
    {
        // Check authorization
        if ($teamMember->team_owner_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $teamMember->delete();

        return back()->with('success', 'Team member removed successfully.');
    }

    /**
     * Accept team member invitation
     */
    public function acceptInvitation($token)
    {
        $teamMember = TeamMember::where('invite_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        // Check if invitation is still valid
        if (!$teamMember->isInvitationValid()) {
            return redirect('/')->with('error', 'This invitation has expired. Please ask your team owner to send a new invitation.');
        }

        // Check if user is logged in
        $user = auth()->user();
        if (!$user || $user->id !== $teamMember->user_id) {
            return redirect('/login')->with('info', 'Please log in with the email address this invitation was sent to.');
        }

        // Accept the invitation
        $teamMember->accept();

        return redirect('/dashboard')->with('success', 'You have been added to the team!');
    }

    /**
     * Reject team member invitation
     */
    public function rejectInvitation($token)
    {
        $teamMember = TeamMember::where('invite_token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $user = auth()->user();
        if (!$user || $user->id !== $teamMember->user_id) {
            abort(403, 'Unauthorized');
        }

        $teamMember->reject();

        return redirect('/dashboard')->with('success', 'You have rejected the team invitation.');
    }
}
