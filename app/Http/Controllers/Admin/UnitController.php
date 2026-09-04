<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
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

    public function edit(Unit $unit): View
    {
        $parentUnits = Unit::where('is_active', true)
            ->where('id', '!=', $unit->id)
            ->whereNotIn('id', $this->descendantIds($unit))
            ->orderBy('name')
            ->get();

        return view('admin.units.edit', compact('unit', 'parentUnits'));
    }

    public function update(UpdateUnitRequest $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validated();

        $unit->update([
            'code'      => $validated['code'],
            'name'      => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Kumpulkan seluruh id turunan unit agar tidak ditawarkan sebagai unit induk,
     * karena akan membentuk hierarki melingkar.
     *
     * @return array<int, string>
     */
    private function descendantIds(Unit $unit): array
    {
        $descendants = [];
        $currentIds  = [$unit->id];

        while ($currentIds !== []) {
            $currentIds = Unit::whereIn('parent_id', $currentIds)
                ->pluck('id')
                ->diff($descendants)
                ->all();

            $descendants = array_merge($descendants, $currentIds);
        }

        return $descendants;
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
