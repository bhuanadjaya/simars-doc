<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Hospital;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Hospital
            'hospital_name'  => ['required', 'string', 'max:255'],
            'hospital_code'  => ['required', 'string', 'max:50', 'unique:hospitals,code'],
            'hospital_email' => ['nullable', 'email', 'max:255'],
            'hospital_phone' => ['nullable', 'string', 'max:30'],
            'hospital_address' => ['nullable', 'string', 'max:500'],
            // Admin user
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            $user = DB::transaction(function () use ($validated) {
                $hospital = Hospital::create([
                    'name'    => $validated['hospital_name'],
                    'code'    => strtoupper($validated['hospital_code']),
                    'email'   => $validated['hospital_email'] ?? null,
                    'phone'   => $validated['hospital_phone'] ?? null,
                    'address' => $validated['hospital_address'] ?? null,
                    'is_active' => true,
                ]);

                // Unit default untuk hospital baru
                $defaultUnit = Unit::create([
                    'hospital_id' => $hospital->id,
                    'code'        => 'UMUM',
                    'name'        => 'Unit Umum',
                    'is_active'   => true,
                ]);

                // Jenis dokumen default
                foreach (
                    [
                        ['code' => 'SPO',      'name' => 'Standar Prosedur Operasional'],
                        ['code' => 'SK',       'name' => 'Surat Keputusan'],
                        ['code' => 'PERDIRUT', 'name' => 'Peraturan Direktur'],
                        ['code' => 'SIP', 'name' => 'Surat Izin Praktik'],
                    ] as $type
                ) {
                    DocumentType::create([
                        'hospital_id' => $hospital->id,
                        'code'        => $type['code'],
                        'name'        => $type['name'],
                        'is_active'   => true,
                    ]);
                }

                $superAdminRole = Role::where('name', 'super_admin')->firstOrFail();

                return User::create([
                    'hospital_id' => $hospital->id,
                    'name'        => $validated['name'],
                    'email'       => $validated['email'],
                    'password'    => Hash::make($validated['password']),
                    'unit_id'     => $defaultUnit->id,
                    'role_id'     => $superAdminRole->id,
                    'is_active'   => true,
                ]);
            });

            Auth::login($user);

            return redirect('/admin')->with('success', 'Selamat datang, ' . $user->name . '! Rumah sakit Anda telah berhasil didaftarkan.');
        } catch (Throwable) {
            return back()->withInput()->with('error', 'Pendaftaran gagal. Silakan coba lagi.');
        }
    }
}
