<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display all system users with filtering, sorting, and pagination.
     */
    public function index(Request $request)
    {
        // ---- Read filters
        $q      = trim((string) $request->get('q', ''));
        $role   = $request->get('role');
        $from   = $request->get('from');
        $to     = $request->get('to');
        $sort   = $request->get('sort', 'created_at');
        $dir    = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // ---- Whitelist sortable columns
        $sortable = ['name', 'email', 'created_at'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'created_at';
        }

        // ---- Query
        $users = User::with('role')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($x) use ($q) {
                    $x->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->whereHas('role', fn($r) => $r->where('name', $role));
            })
            ->when($from, function ($query) use ($from) {
                if (Carbon::hasFormat($from, 'Y-m-d')) {
                    $query->whereDate('created_at', '>=', $from);
                }
            })
            ->when($to, function ($query) use ($to) {
                if (Carbon::hasFormat($to, 'Y-m-d')) {
                    $query->whereDate('created_at', '<=', $to);
                }
            })
            ->orderBy($sort, $dir)
            ->paginate(15)
            ->appends($request->query());

        // ---- Roles for dropdown
        $roles = Role::orderBy('name')->pluck('name')->all();

        return view('admin.users.index', compact('users', 'roles', 'q', 'role', 'from', 'to', 'sort', 'dir'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing an existing user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($request->only('name', 'email', 'role_id'));

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Reset a user’s password.
     */
    public function resetPassword(User $user)
    {
        $newPassword = 'password123'; // You can make this random if you wish
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name}'s password has been reset to '{$newPassword}'.");
    }

    /**
     * Remove a user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
