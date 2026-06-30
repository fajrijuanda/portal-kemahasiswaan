<?php

namespace Tests\Feature;

use App\Models\AchievementQuota;
use App\Models\Beasiswa;
use App\Models\CareerPost;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Ormawa;
use App\Models\OrmawaProposal;
use App\Models\PressRelease;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\ScholarshipType;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MvpRevisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_can_submit_beasiswa(): void
    {
        $setup = $this->setupAcademicData();
        $user = $this->userWithRole('mahasiswa', ['prodi_id' => $setup['prodi']->id, 'nim' => '20260001']);
        $type = ScholarshipType::where('nama', 'KIP')->firstOrFail();

        $this->actingAs($user)
            ->post(route('student.beasiswa.store'), [
                'scholarship_type_id' => $type->id,
                'nominal' => 1500000,
                'catatan' => 'Pengajuan KIP',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('beasiswas', [
            'created_by' => $user->id,
            'prodi_id' => $setup['prodi']->id,
            'scholarship_type_id' => $type->id,
            'status' => 'Diajukan',
            'jenis_beasiswa' => 'KIP',
        ]);
    }

    public function test_ormawa_can_submit_proposal_and_reimbursement(): void
    {
        Storage::fake('public');
        $setup = $this->setupAcademicData();
        $user = $this->userWithRole('ormawa', ['prodi_id' => $setup['prodi']->id]);
        $ormawa = Ormawa::create(['user_id' => $user->id, 'nama' => 'BEM Test', 'status' => 'Aktif']);

        $this->actingAs($user)
            ->post(route('ormawa.proposals.store'), [
                'judul' => 'Seminar Ormawa',
                'proposal_path' => UploadedFile::fake()->create('proposal.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('ormawa.reimbursements.store'), [
                'jenis_reimbursement' => 'Transport',
                'nama_kegiatan' => 'Seminar Ormawa',
                'nominal' => 500000,
                'foto_path' => UploadedFile::fake()->image('foto.jpg'),
                'surat_tugas_path' => UploadedFile::fake()->create('surat.pdf', 120, 'application/pdf'),
                'sertifikat_path' => UploadedFile::fake()->create('sertifikat.pdf', 120, 'application/pdf'),
                'link_penyelenggara' => 'https://example.com/event',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ormawa_proposals', ['ormawa_id' => $ormawa->id, 'status' => 'Diajukan']);
        $this->assertDatabaseHas('events', ['ormawa_id' => $ormawa->id, 'status' => 'Diajukan', 'nama_pengaju' => 'BEM Test']);
    }

    public function test_admin_can_verify_prestasi_and_quota_updates(): void
    {
        $setup = $this->setupAcademicData();
        $admin = $this->userWithRole('admin');
        $competition = Competition::firstOrFail();
        AchievementQuota::create(['semester_id' => $setup['semester']->id, 'prodi_id' => $setup['prodi']->id, 'slot_prestasi' => 3]);
        $prestasi = Prestasi::create([
            'semester_id' => $setup['semester']->id,
            'prodi_id' => $setup['prodi']->id,
            'competition_id' => $competition->id,
            'nama_mahasiswa' => 'Mahasiswa Test',
            'nama_kegiatan' => 'Lomba Test',
            'kategori_event' => 'Perorangan',
            'scope' => 'Nasional',
            'juara' => '1',
            'status' => 'Diajukan',
        ]);

        $this->actingAs($admin)
            ->put(route('prestasi.update', $prestasi), [
                'semester_id' => $setup['semester']->id,
                'prodi_id' => $setup['prodi']->id,
                'competition_id' => $competition->id,
                'nama_mahasiswa' => 'Mahasiswa Test',
                'nama_kegiatan' => 'Lomba Test',
                'kategori_event' => 'Perorangan',
                'scope' => 'Nasional',
                'juara' => '1',
                'status' => 'Terverifikasi',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('achievement_quotas', [
            'semester_id' => $setup['semester']->id,
            'prodi_id' => $setup['prodi']->id,
            'terpakai' => 1,
        ]);
    }

    public function test_public_page_only_shows_published_content(): void
    {
        $user = $this->userWithRole('admin');
        PressRelease::create(['title' => 'Published Press', 'slug' => 'published-press', 'status' => 'Published', 'published_at' => now(), 'created_by' => $user->id]);
        PressRelease::create(['title' => 'Draft Press', 'slug' => 'draft-press', 'status' => 'Draft', 'created_by' => $user->id]);
        CareerPost::create(['type' => 'Loker', 'title' => 'Published Loker', 'status' => 'Published', 'published_at' => now(), 'created_by' => $user->id]);
        CareerPost::create(['type' => 'Job Fair', 'title' => 'Draft Job Fair', 'status' => 'Draft', 'created_by' => $user->id]);

        $this->get(route('public.index'))
            ->assertOk()
            ->assertSee('Published Press')
            ->assertSee('Published Loker')
            ->assertDontSee('Draft Press')
            ->assertDontSee('Draft Job Fair');

        $this->get(route('public.profile'))->assertOk()->assertSee('Profil');
        $this->get(route('public.services'))->assertOk()->assertSee('Layanan');
        $this->get(route('public.news'))
            ->assertOk()
            ->assertSee('Published Press')
            ->assertDontSee('Draft Press');
        $this->get(route('public.news.show', 'published-press'))
            ->assertOk()
            ->assertSee('Published Press');
        $this->get(route('public.services.show', 'prestasi-mahasiswa'))
            ->assertOk()
            ->assertSee('Prestasi Mahasiswa');
        $this->get(route('public.links'))
            ->assertOk()
            ->assertSee('Published Loker')
            ->assertDontSee('Draft Job Fair');
    }

    public function test_new_mvp_pages_render_for_their_roles(): void
    {
        $setup = $this->setupAcademicData();
        $admin = $this->userWithRole('admin');
        $kabag = $this->userWithRole('kabag');
        $student = $this->userWithRole('mahasiswa', ['prodi_id' => $setup['prodi']->id, 'nim' => '20260002']);
        $ormawaUser = $this->userWithRole('ormawa', ['prodi_id' => $setup['prodi']->id]);
        Ormawa::create(['user_id' => $ormawaUser->id, 'nama' => 'HIMA Test', 'status' => 'Aktif']);

        $this->actingAs($admin)->get(route('prestasi.index'))->assertOk();
        $this->actingAs($admin)->get(route('prestasi.table'))->assertOk();
        $this->actingAs($admin)->get(route('event.index'))->assertOk();
        $this->actingAs($admin)->get(route('event.table'))->assertOk();
        $this->actingAs($admin)->get(route('reimburse.table'))->assertOk();
        $this->actingAs($admin)->get(route('unit-activities.index', 'humas-marketing'))->assertOk();
        $this->actingAs($admin)->get(route('ormawa-admin.index', 'data-ormawa'))->assertOk();
        $this->actingAs($admin)->get(route('master-data.index', 'competitions'))->assertOk();
        $this->actingAs($admin)->get(route('master-data.index', 'quotas'))->assertOk();
        $this->actingAs($admin)->get(route('publications.index', 'careers'))->assertOk();
        $this->actingAs($kabag)->get(route('publications.index', 'berita'))->assertOk();
        $this->actingAs($student)->get(route('student.submissions'))->assertOk();
        $this->actingAs($ormawaUser)->get(route('ormawa.panel'))->assertOk();
        $this->get(route('public.index'))->assertOk();
    }

    public function test_legacy_routes_redirect_to_group_pages(): void
    {
        $this->setupAcademicData();
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get('/prestasi')->assertOk()->assertSee('Pilih tabel');
        $this->actingAs($admin)->get('/event')->assertOk()->assertSee('Event dan Reimbursement');
        $this->actingAs($admin)->get('/data/prestasi')->assertRedirect(route('prestasi.table'));
        $this->actingAs($admin)->get('/data/event')->assertRedirect(route('event.table'));
        $this->actingAs($admin)->get('/unit-data/humas-marketing')->assertRedirect(route('unit-activities.index', 'humas-marketing'));
        $this->actingAs($admin)->get('/master-data/prodi')->assertRedirect(route('master-data.index', 'prodi'));
        $this->actingAs($admin)->get('/master-ormawa')->assertRedirect(route('ormawa-admin.index', 'data-ormawa'));
        $this->actingAs($admin)->get('/karir')->assertRedirect(route('publications.index', 'careers'));
        $this->actingAs($admin)->get('/publikasi/press-releases')->assertRedirect(route('publications.index', 'berita'));
    }

    public function test_master_pages_support_search_and_filters(): void
    {
        $setup = $this->setupAcademicData();
        $admin = $this->userWithRole('admin');
        Prodi::create(['nama' => 'Akuntansi', 'kode' => 'AKT', 'fakultas' => 'Ekonomi dan Bisnis']);
        Semester::create(['nama' => 'Genap 2026/2027', 'tahun_akademik' => '2026/2027', 'periode' => 'Genap', 'is_active' => false]);
        ScholarshipType::create(['nama' => 'Beasiswa Riset Khusus', 'is_active' => true]);
        ScholarshipType::create(['nama' => 'Draft Bantuan', 'is_active' => false]);
        AchievementQuota::create(['semester_id' => $setup['semester']->id, 'prodi_id' => $setup['prodi']->id, 'slot_prestasi' => 4]);

        $this->actingAs($admin)->get(route('master-data.index', ['section' => 'prodi', 'q' => 'Akuntansi']))
            ->assertOk()
            ->assertSee('Akuntansi')
            ->assertDontSee('Teknik Informatika');

        $this->actingAs($admin)->get(route('master-data.index', ['section' => 'semester', 'periode' => 'Genap']))
            ->assertOk()
            ->assertSee('Genap 2026/2027')
            ->assertDontSee('<td data-label="Nama"><span class="ubp-table-primary">Ganjil 2026/2027</span></td>', false);

        $this->actingAs($admin)->get(route('master-data.index', ['section' => 'scholarship-types', 'status' => 'active']))
            ->assertOk()
            ->assertSee('Beasiswa Riset Khusus')
            ->assertDontSee('Draft Bantuan');

        $this->actingAs($admin)->get(route('master-data.index', ['section' => 'quotas', 'prodi_id' => $setup['prodi']->id]))
            ->assertOk()
            ->assertSee('Teknik Informatika');
    }

    private function setupAcademicData(): array
    {
        $prodi = Prodi::create(['nama' => 'Teknik Informatika', 'kode' => 'TI']);
        $semester = Semester::create(['nama' => 'Ganjil 2026/2027', 'tahun_akademik' => '2026/2027', 'periode' => 'Ganjil', 'is_active' => true]);

        return compact('prodi', 'semester');
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
