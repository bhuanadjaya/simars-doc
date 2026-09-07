<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Hospital;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function hospitals(Request $request): View
    {
        $query = Hospital::withCount(['users', 'documents'])->orderBy('name');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('code', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $hospitals = $query->paginate(20)->withQueryString();

        return view('system.hospitals', compact('hospitals'));
    }

    public function users(Request $request): View
    {
        $query = User::with(['role', 'hospital', 'unit'])->orderBy('name');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('hospital')) {
            $query->where('hospital_id', $request->hospital);
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn ($q) => $q->where('name', $request->role));
        }

        $users     = $query->paginate(20)->withQueryString();
        $hospitals = Hospital::orderBy('name')->get(['id', 'name', 'code']);

        return view('system.users', compact('users', 'hospitals'));
    }

    public function editUser(User $user): View
    {
        $hospitals = Hospital::orderBy('name')->get(['id', 'name', 'code']);
        $units     = $user->hospital_id
            ? Unit::where('hospital_id', $user->hospital_id)->orderBy('name')->get(['id', 'name', 'code'])
            : collect();
        $roles     = Role::where('name', '!=', 'system_admin')->orderBy('name')->get();

        return view('system.edit-user', compact('user', 'hospitals', 'units', 'roles'));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_id' => ['nullable', 'exists:hospitals,id'],
            'unit_id'     => ['nullable', 'exists:units,id'],
            'role_id'     => ['required', 'exists:roles,id'],
        ]);

        $user->update($validated);

        return redirect()->route('system.users')
            ->with('success', 'User "' . $user->name . '" berhasil diperbarui.');
    }

    public function unitsByHospital(Hospital $hospital): JsonResponse
    {
        $units = Unit::where('hospital_id', $hospital->id)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($units);
    }

    public function activityLog(Request $request): View
    {
        $query = ActivityLog::with(['user', 'user.hospital', 'document'])->latest();

        if ($request->filled('hospital')) {
            $query->where('hospital_id', $request->hospital);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs      = $query->paginate(30)->withQueryString();
        $hospitals = Hospital::orderBy('name')->get(['id', 'name', 'code']);
        $actions   = ActivityLog::select('action')->distinct()->pluck('action')->sort()->values();

        return view('system.activity-log', compact('logs', 'hospitals', 'actions'));
    }
}
