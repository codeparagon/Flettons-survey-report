<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        $user = $this->userService->findUser($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }
        
        // Eager load the role relationship
        $user->load('role');
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $this->userService->createUser($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the user.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $user = $this->userService->findUser($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the user.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $validated['password'] = $request->password;
        }

        $this->userService->updateUser($id, $validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the user.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        $this->userService->deleteUser($id);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Activate a user.
     *
     * @param int $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(int $user)
    {
        $this->userService->activateUser($user);

        return redirect()->route('admin.users.index')
            ->with('success', 'User activated successfully.');
    }

    /**
     * Deactivate a user.
     *
     * @param int $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(int $user)
    {
        // Prevent deactivating yourself
        if ($user === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $this->userService->deactivateUser($user);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deactivated successfully.');
    }

    /**
     * Impersonate a user.
     *
     * @param int $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonate(int $user)
    {
        $targetUser = $this->userService->findUser($user);
        
        if (!$targetUser) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }

        // Prevent impersonating yourself
        if ($user === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot impersonate yourself.');
        }

        // Store original user ID in session
        session(['impersonating' => auth()->id()]);
        
        // Log in as the target user
        auth()->login($targetUser);

        return redirect()->route('home')
            ->with('success', 'You are now impersonating ' . $targetUser->name . '.');
    }

    /**
     * Reset user password.
     *
     * @param int $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(int $user)
    {
        $targetUser = $this->userService->findUser($user);
        
        if (!$targetUser) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User not found.');
        }

        // Generate a random password
        $newPassword = \Illuminate\Support\Str::random(12);
        $this->userService->resetUserPassword($user, $newPassword);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password reset successfully for ' . $targetUser->name . '. New password: ' . $newPassword);
    }

    /**
     * Show user activity log.
     *
     * @param int $user
     * @return \Illuminate\View\View
     */
    public function activity(int $user)
    {
        $targetUser = $this->userService->findUser($user);
        
        if (!$targetUser) {
            abort(404, 'User not found');
        }

        // Load role relationship
        $targetUser->load('role');

        // For now, return a simple view with basic activity info
        // You can expand this later to show actual activity logs if you have an activity log system
        return view('admin.users.activity', compact('targetUser'));
    }
}











