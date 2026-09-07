<?php

namespace Tests\Feature;

use App\Models\Hospital;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Hospital $hospital;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hospital = Hospital::create(['name' => 'RS Uji', 'code' => 'RSU']);
        $role           = Role::create([
            'name'        => 'super_admin',
            'description' => 'Super Admin',
            'permissions' => ['*'],
        ]);

        // users.unit_id NOT NULL, jadi admin butuh unit sendiri
        $adminUnit = $this->unit('ADM', 'Administrasi');

        $this->admin = User::factory()->create([
            'hospital_id' => $this->hospital->id,
            'role_id'     => $role->id,
            'unit_id'     => $adminUnit->id,
        ]);
    }

    private function unit(string $code, string $name, ?string $parentId = null): Unit
    {
        return Unit::create([
            'hospital_id' => $this->hospital->id,
            'code'        => $code,
            'name'        => $name,
            'parent_id'   => $parentId,
            'is_active'   => true,
        ]);
    }

    public function test_it_updates_code_name_and_parent(): void
    {
        $parent = $this->unit('DIR', 'Direktorat');
        $unit   = $this->unit('IGD', 'Instalasi Gawat Darurat');

        $this->actingAs($this->admin)
            ->put(route('admin.units.update', $unit), [
                'code'      => 'IGDX',
                'name'      => 'IGD Terpadu',
                'parent_id' => $parent->id,
            ])
            ->assertRedirect(route('admin.units.index'))
            ->assertSessionHas('success');

        $unit->refresh();
        $this->assertSame('IGDX', $unit->code);
        $this->assertSame('IGD Terpadu', $unit->name);
        $this->assertSame($parent->id, $unit->parent_id);
    }

    public function test_it_can_clear_the_parent(): void
    {
        $parent = $this->unit('DIR', 'Direktorat');
        $unit   = $this->unit('IGD', 'IGD', $parent->id);

        $this->actingAs($this->admin)
            ->put(route('admin.units.update', $unit), [
                'code'      => 'IGD',
                'name'      => 'IGD',
                'parent_id' => '',
            ])
            ->assertSessionHasNoErrors();

        $this->assertNull($unit->refresh()->parent_id);
    }

    public function test_it_keeps_its_own_code_on_update(): void
    {
        $unit = $this->unit('IGD', 'IGD');

        $this->actingAs($this->admin)
            ->put(route('admin.units.update', $unit), [
                'code' => 'IGD',
                'name' => 'IGD Baru',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('IGD Baru', $unit->refresh()->name);
    }

    public function test_it_rejects_a_code_used_by_another_unit(): void
    {
        $this->unit('DIR', 'Direktorat');
        $unit = $this->unit('IGD', 'IGD');

        $this->actingAs($this->admin)
            ->put(route('admin.units.update', $unit), [
                'code' => 'DIR',
                'name' => 'IGD',
            ])
            ->assertSessionHasErrors('code');

        $this->assertSame('IGD', $unit->refresh()->code);
    }

    public function test_it_rejects_a_unit_as_its_own_parent(): void
    {
        $unit = $this->unit('IGD', 'IGD');

        $this->actingAs($this->admin)
            ->put(route('admin.units.update', $unit), [
                'code'      => 'IGD',
                'name'      => 'IGD',
                'parent_id' => $unit->id,
            ])
            ->assertSessionHasErrors('parent_id');

        $this->assertNull($unit->refresh()->parent_id);
    }

    public function test_it_rejects_a_descendant_as_parent(): void
    {
        $root  = $this->unit('DIR', 'Direktorat');
        $child = $this->unit('YAN', 'Pelayanan', $root->id);
        $grand = $this->unit('IGD', 'IGD', $child->id);

        // Menjadikan cucu sebagai induk kakek akan membentuk siklus.
        $this->actingAs($this->admin)
            ->put(route('admin.units.update', $root), [
                'code'      => 'DIR',
                'name'      => 'Direktorat',
                'parent_id' => $grand->id,
            ])
            ->assertSessionHasErrors('parent_id');

        $this->assertNull($root->refresh()->parent_id);
    }

    public function test_edit_page_hides_self_and_descendants_from_parent_options(): void
    {
        $root    = $this->unit('DIR', 'Direktorat');
        $child   = $this->unit('YAN', 'Pelayanan', $root->id);
        $grand   = $this->unit('IGD', 'IGD', $child->id);
        $sibling = $this->unit('KEU', 'Keuangan');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.units.edit', $root))
            ->assertOk();

        $options = $response->viewData('parentUnits')->pluck('id')->all();

        $this->assertNotContains($root->id, $options, 'unit itu sendiri tidak boleh muncul');
        $this->assertNotContains($child->id, $options, 'anak tidak boleh muncul');
        $this->assertNotContains($grand->id, $options, 'cucu tidak boleh muncul');
        $this->assertContains($sibling->id, $options, 'unit lain harus tetap muncul');
    }
}
