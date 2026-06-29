<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardKemahasiswaanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_core_pages_and_charts(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@ubpkarawang.ac.id')->firstOrFail();

        foreach ([
            '/dashboard',
            '/prestasi',
            '/prestasi/mahasiswa',
            '/event',
            '/event/kegiatan',
            '/event/reimburse',
            '/tracer',
            '/tracer/data',
            '/beasiswa',
            '/beasiswa/data',
            '/master-data/prodi',
            '/master-data/semester',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }

        $this->actingAs($admin)->get('/management-user')->assertForbidden();
        $this->actingAs($admin)->get('/data/prestasi')->assertRedirect('/prestasi/mahasiswa');
        $this->actingAs($admin)->get('/data/event')->assertRedirect('/event/kegiatan');
        $this->actingAs($admin)->get('/master/prodi')->assertRedirect('/master-data/prodi');
        $this->actingAs($admin)->get('/claim-transport')->assertRedirect('/event/reimburse');
        $this->actingAs($admin)->get('/claim-fasilitas')->assertRedirect('/event/reimburse');

        foreach ([
            '/dashboard/charts/prestasi-by-semester',
            '/dashboard/charts/prestasi-by-prodi',
            '/dashboard/charts/claims',
            '/dashboard/charts/beasiswa',
            '/dashboard/charts/tracer-study',
        ] as $path) {
            $this->actingAs($admin)
                ->getJson($path)
                ->assertOk()
                ->assertJsonStructure(['labels', 'data', 'links']);
        }
    }

    public function test_kaprodi_is_scoped_to_own_prodi(): void
    {
        $this->seed();

        $kaprodi = User::role('kaprodi')->whereNotNull('prodi_id')->firstOrFail();

        $this->actingAs($kaprodi)
            ->get('/prestasi')
            ->assertOk()
            ->assertSee('Pilih tabel');

        $this->actingAs($kaprodi)
            ->get('/prestasi/mahasiswa')
            ->assertOk()
            ->assertSee($kaprodi->prodi->nama);

        $this->actingAs($kaprodi)
            ->get('/management-user')
            ->assertForbidden();
    }

    public function test_super_user_can_manage_users(): void
    {
        $this->seed();

        $superUser = User::where('email', 'super@ubpkarawang.ac.id')->firstOrFail();

        $this->actingAs($superUser)
            ->get('/management-user')
            ->assertOk()
            ->assertSee('Management User')
            ->assertSee('Tambah User');
    }
}
