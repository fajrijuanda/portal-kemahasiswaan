<?php

namespace Tests\Feature;

use App\Models\Prodi;
use App\Models\Semester;
use App\Models\UnitActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitActivityInputTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_unit_activity_and_rekap_uses_it(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@ubpkarawang.ac.id')->firstOrFail();
        $semester = Semester::firstOrFail();
        $prodi = Prodi::firstOrFail();

        $this->actingAs($admin)->get('/unit/humas-marketing')
            ->assertOk()
            ->assertSee('Humas Marketing')
            ->assertSee('Tambah Data');

        $this->actingAs($admin)->post('/unit/humas-marketing', [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'judul' => 'Publikasi Kampus Baru',
            'penanggung_jawab' => 'Tim Humas',
            'tanggal' => '2026-06-20',
            'status' => 'Berjalan',
            'catatan' => 'Konten promosi untuk kanal digital.',
        ])->assertRedirect('/unit/humas-marketing');

        $activity = UnitActivity::where('unit', 'humas-marketing')->firstOrFail();
        $this->assertSame('Publikasi Kampus Baru', $activity->judul);

        $this->actingAs($admin)
            ->getJson('/dashboard/charts/summary-cards')
            ->assertOk()
            ->assertJsonPath('Humas Marketing.data.0', 1);

        $this->actingAs($admin)
            ->getJson('/dashboard/charts/unit/humas-marketing')
            ->assertOk()
            ->assertJsonPath('labels.0', 'Berjalan')
            ->assertJsonPath('data.0', 1);
    }
}