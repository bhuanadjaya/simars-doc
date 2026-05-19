<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private ActivityLogService $activityLog) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        $user->update(['last_login_at' => now()]);

        $this->activityLog->log($user, 'login');

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath($user->role->name));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $this->activityLog->log($user, 'logout');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function redirectPath(string $role): string
    {
        return match ($role) {
            'super_admin', 'admin_unit', 'auditor' => '/admin',
            default => '/portal/documents',
        };
    }
}
