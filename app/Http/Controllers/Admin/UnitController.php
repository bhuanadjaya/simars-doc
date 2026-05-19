<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Models\Document;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $query = Unit::with('parent')->withCount('users');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $units = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.units.index', compact('units'));
    }

    public function create(): View
    {
        $parentUnits = Unit::where('is_active', true)->orderBy('name')->get();

        return view('admin.units.create', compact('parentUnits'));
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Unit::create([
            'code'      => $validated['code'],
            'name'      => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function deactivate(Unit $unit): RedirectResponse
    {
        $hasActiveDocuments = Document::where('owner_unit_id', $unit->id)
            ->where('status', 'active')
            ->exists();

        if ($hasActiveDocuments) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Unit tidak dapat dinonaktifkan karena masih memiliki dokumen aktif.');
        }

        $unit->update(['is_active' => false]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil dinonaktifkan.');
    }

    public function activate(Unit $unit): RedirectResponse
    {
        $unit->update(['is_active' => true]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil diaktifkan kembali.');
    }
}
