<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        // Only admins can access user management
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->is_admin) {
                abort(403, 'Unauthorized - Admin access required');
            }
            return $next($request);
        });
    }

    /**
     * Display all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by admin status
        if ($request->filled('admin')) {
            $query->where('is_admin', $request->input('admin') == 'yes' ? 1 : 0);
        }

        // Sort
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_admin'] = $request->boolean('is_admin');

            $user = User::create($validated);

            Log::info('User created by admin', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', "User {$user->email} created successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? null,
            ]);

            return back()->with('error', 'Failed to create user.');
        }
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        $activeSubscription = $user->activeSubscription;
        $teamOwned = $user->teamMembers()->where('status', 'active')->count();
        $teamMember = $user->memberOfTeams()->where('status', 'active')->count();
        $accounts = $user->whatsappAccounts()->count();

        return view('admin.users.show', compact('user', 'activeSubscription', 'teamOwned', 'teamMember', 'accounts'));
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        try {
            if ($validated['password']) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_admin'] = $request->boolean('is_admin');

            $user->update($validated);

            Log::info('User updated by admin', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', "User {$user->email} updated successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Failed to update user.');
        }
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting self
            if ($user->id === auth()->id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            $email = $user->email;
            $user->delete();

            Log::info('User deleted by admin', [
                'admin_id' => auth()->id(),
                'deleted_user_id' => $user->id,
                'email' => $email,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "User {$email} deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Failed to delete user.');
        }
    }
}
