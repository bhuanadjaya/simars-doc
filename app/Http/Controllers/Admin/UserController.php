<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['unit', 'role'])->latest();

        if ($request->filled('unit')) {
            $query->where('unit_id', $request->unit);
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('employee_id', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'units', 'roles'));
    }

    public function create(): View
    {
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('units', 'roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name'        => $validated['name'],
            'employee_id' => $validated['employee_id'] ?? null,
            'email'       => $validated['email'],
            'unit_id'     => $validated['unit_id'],
            'role_id'     => $validated['role_id'],
            'password'    => Hash::make($validated['password']),
            'is_active'   => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'units', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $user->update([
            'name'        => $validated['name'],
            'employee_id' => $validated['employee_id'] ?? null,
            'email'       => $validated['email'],
            'unit_id'     => $validated['unit_id'],
            'role_id'     => $validated['role_id'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dinonaktifkan.');
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil diaktifkan kembali.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $newPassword = Str::password(12);
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->route('admin.users.index')
            ->with('success', "Password berhasil direset.")
            ->with('generated_password', $newPassword)
            ->with('generated_password_user', $user->name);
    }
}
