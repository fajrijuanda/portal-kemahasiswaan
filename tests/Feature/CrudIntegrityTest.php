<?php

namespace Tests\Feature;

use App\Models\AchievementQuota;
use App\Models\Beasiswa;
use App\Models\CareerPost;
use App\Models\Competition;
use App\Models\Event;
use App\Models\Ormawa;
use App\Models\PressRelease;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\ScholarshipType;
use App\Models\Semester;
use App\Models\TracerStudy;
use App\Models\UnitActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CrudIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_record_modules_can_create_update_and_delete(): void
    {
        $this->seed();
        $admin = $this->adminUser();
        $semester = Semester::firstOrFail();
        $prodi = Prodi::firstOrFail();
        $competition = Competition::firstOrFail();
        $scholarshipType = ScholarshipType::firstOrFail();

        $this->actingAs($admin)->post(route('prestasi.store'), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'competition_id' => $competition->id,
            'nama_mahasiswa' => 'CRUD Prestasi',
            'nim' => '990001',
            'nama_kegiatan' => 'Lomba CRUD',
            'kategori_event' => 'Perorangan',
            'scope' => 'Nasional',
            'juara' => '1',
            'status' => 'Diajukan',
        ])->assertRedirect(route('prestasi.table'));

        $prestasi = Prestasi::where('nama_mahasiswa', 'CRUD Prestasi')->firstOrFail();
        $this->actingAs($admin)->put(route('prestasi.update', $prestasi), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'competition_id' => $competition->id,
            'nama_mahasiswa' => 'CRUD Prestasi Updated',
            'nim' => '990001',
            'nama_kegiatan' => 'Lomba CRUD',
            'kategori_event' => 'Perorangan',
            'scope' => 'Internasional',
            'juara' => '2',
            'status' => 'Terverifikasi',
        ])->assertRedirect(route('prestasi.table'));
        $this->assertDatabaseHas('prestasis', ['id' => $prestasi->id, 'nama_mahasiswa' => 'CRUD Prestasi Updated', 'status' => 'Terverifikasi']);
        $this->actingAs($admin)->delete(route('prestasi.destroy', $prestasi))->assertRedirect();
        $this->assertDatabaseMissing('prestasis', ['id' => $prestasi->id]);

        $this->actingAs($admin)->post(route('event.store'), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'nama_pengaju' => 'CRUD Event',
            'nim' => '990002',
            'jenis_reimbursement' => 'Transport',
            'nama_kegiatan' => 'Kegiatan CRUD',
            'nominal' => 250000,
            'status' => 'Diajukan',
        ])->assertRedirect(route('event.table'));

        $event = Event::where('nama_pengaju', 'CRUD Event')->firstOrFail();
        $this->actingAs($admin)->put(route('records.update', ['reimburse', $event]), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'nama_pengaju' => 'CRUD Reimburse Updated',
            'nim' => '990002',
            'jenis_reimbursement' => 'Akomodasi',
            'nama_kegiatan' => 'Kegiatan CRUD',
            'nominal' => 300000,
            'status' => 'Disetujui',
        ])->assertRedirect(route('reimburse.table'));
        $this->assertDatabaseHas('events', ['id' => $event->id, 'nama_pengaju' => 'CRUD Reimburse Updated', 'status' => 'Disetujui']);
        $this->actingAs($admin)->delete(route('records.destroy', ['reimburse', $event]))->assertRedirect();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);

        $this->actingAs($admin)->post(route('beasiswa.store'), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'nama_mahasiswa' => 'CRUD Beasiswa',
            'scholarship_type_id' => $scholarshipType->id,
            'nominal' => 1500000,
            'status' => 'Diajukan',
        ])->assertRedirect(route('beasiswa.table'));
        $beasiswa = Beasiswa::where('nama_mahasiswa', 'CRUD Beasiswa')->firstOrFail();
        $this->actingAs($admin)->put(route('beasiswa.update', $beasiswa), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'nama_mahasiswa' => 'CRUD Beasiswa Updated',
            'scholarship_type_id' => $scholarshipType->id,
            'nominal' => 1750000,
            'status' => 'Aktif',
        ])->assertRedirect(route('beasiswa.table'));
        $this->assertDatabaseHas('beasiswas', ['id' => $beasiswa->id, 'nama_mahasiswa' => 'CRUD Beasiswa Updated', 'status' => 'Aktif']);
        $this->actingAs($admin)->delete(route('beasiswa.destroy', $beasiswa))->assertRedirect();
        $this->assertDatabaseMissing('beasiswas', ['id' => $beasiswa->id]);

        $this->actingAs($admin)->post(route('tracer-study.store'), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'jumlah_mahasiswa' => 40,
            'jumlah_input' => 30,
            'periode_yudisium' => 'Juli 2026',
            'status' => 'Berjalan',
        ])->assertRedirect(route('tracer.table'));
        $tracer = TracerStudy::where('periode_yudisium', 'Juli 2026')->firstOrFail();
        $this->actingAs($admin)->put(route('tracer-study.update', $tracer), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'jumlah_mahasiswa' => 40,
            'jumlah_input' => 40,
            'periode_yudisium' => 'Juli 2026',
            'status' => 'Lengkap',
        ])->assertRedirect(route('tracer.table'));
        $this->assertDatabaseHas('tracer_studies', ['id' => $tracer->id, 'jumlah_input' => 40, 'status' => 'Lengkap']);
        $this->actingAs($admin)->delete(route('tracer-study.destroy', $tracer))->assertRedirect();
        $this->assertDatabaseMissing('tracer_studies', ['id' => $tracer->id]);

        $this->actingAs($admin)->post(route('kuota-prestasi.store'), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'slot_prestasi' => 8,
            'terpakai' => 0,
        ])->assertRedirect(route('kuota-prestasi.table'));
        $quota = AchievementQuota::where('semester_id', $semester->id)->where('prodi_id', $prodi->id)->firstOrFail();
        $this->actingAs($admin)->put(route('kuota-prestasi.update', $quota), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'slot_prestasi' => 10,
            'terpakai' => 1,
        ])->assertRedirect(route('kuota-prestasi.table'));
        $this->assertDatabaseHas('achievement_quotas', ['id' => $quota->id, 'slot_prestasi' => 10]);
        $this->actingAs($admin)->delete(route('kuota-prestasi.destroy', $quota))->assertRedirect();
        $this->assertDatabaseMissing('achievement_quotas', ['id' => $quota->id]);
    }

    public function test_master_unit_publication_and_user_crud_work(): void
    {
        $this->seed();
        $admin = $this->adminUser();
        $super = User::where('email', 'super@ubpkarawang.ac.id')->firstOrFail();
        $semester = Semester::firstOrFail();
        $prodi = Prodi::firstOrFail();

        $this->actingAs($admin)->post(route('master.prodi.store'), ['nama' => 'CRUD Prodi', 'kode' => 'CRD', 'fakultas' => 'Teknologi'])->assertRedirect();
        $crudProdi = Prodi::where('nama', 'CRUD Prodi')->firstOrFail();
        $this->actingAs($admin)->put(route('master.prodi.update', $crudProdi), ['nama' => 'CRUD Prodi Updated', 'kode' => 'CRU', 'fakultas' => 'Teknologi'])->assertRedirect();
        $this->assertDatabaseHas('prodis', ['id' => $crudProdi->id, 'nama' => 'CRUD Prodi Updated']);
        $this->actingAs($admin)->delete(route('master.prodi.destroy', $crudProdi))->assertRedirect();
        $this->assertDatabaseMissing('prodis', ['id' => $crudProdi->id]);

        $this->actingAs($admin)->post(route('master.semester.store'), ['nama' => 'CRUD 2027/2028', 'tahun_akademik' => '2027/2028', 'periode' => 'Ganjil', 'is_active' => 0])->assertRedirect();
        $crudSemester = Semester::where('nama', 'CRUD 2027/2028')->firstOrFail();
        $this->actingAs($admin)->put(route('master.semester.update', $crudSemester), ['nama' => 'CRUD 2027/2028 Updated', 'tahun_akademik' => '2027/2028', 'periode' => 'Genap', 'is_active' => 0])->assertRedirect();
        $this->assertDatabaseHas('semesters', ['id' => $crudSemester->id, 'periode' => 'Genap']);
        $this->actingAs($admin)->delete(route('master.semester.destroy', $crudSemester))->assertRedirect();
        $this->assertDatabaseMissing('semesters', ['id' => $crudSemester->id]);

        $this->actingAs($admin)->post(route('master.simple.store', 'competitions'), ['nama' => 'CRUD Competition', 'is_active' => 1])->assertRedirect();
        $competition = Competition::where('nama', 'CRUD Competition')->firstOrFail();
        $this->actingAs($admin)->put(route('master.simple.update', ['competitions', $competition]), ['nama' => 'CRUD Competition Updated', 'is_active' => 0])->assertRedirect();
        $this->assertDatabaseHas('competitions', ['id' => $competition->id, 'nama' => 'CRUD Competition Updated', 'is_active' => false]);
        $this->actingAs($admin)->delete(route('master.simple.destroy', ['competitions', $competition]))->assertRedirect();
        $this->assertDatabaseMissing('competitions', ['id' => $competition->id]);

        $this->actingAs($admin)->post(route('master.ormawa.store'), ['nama' => 'CRUD Ormawa', 'jenis' => 'UKM', 'status' => 'Aktif'])->assertRedirect();
        $ormawa = Ormawa::where('nama', 'CRUD Ormawa')->firstOrFail();
        $this->actingAs($admin)->put(route('master.ormawa.update', $ormawa), ['nama' => 'CRUD Ormawa Updated', 'jenis' => 'HIMA', 'status' => 'Nonaktif'])->assertRedirect();
        $this->assertDatabaseHas('ormawas', ['id' => $ormawa->id, 'nama' => 'CRUD Ormawa Updated', 'status' => 'Nonaktif']);
        $this->actingAs($admin)->delete(route('master.ormawa.destroy', $ormawa))->assertRedirect();
        $this->assertDatabaseMissing('ormawas', ['id' => $ormawa->id]);

        $this->actingAs($admin)->post(route('unit-activities.store', 'humas-marketing'), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'judul' => 'CRUD Unit',
            'penanggung_jawab' => 'Admin',
            'status' => 'Draft',
        ])->assertRedirect(route('unit-activities.index', 'humas-marketing'));
        $activity = UnitActivity::where('judul', 'CRUD Unit')->firstOrFail();
        $this->actingAs($admin)->put(route('unit-activities.update', ['humas-marketing', $activity]), [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'judul' => 'CRUD Unit Updated',
            'penanggung_jawab' => 'Admin',
            'status' => 'Selesai',
        ])->assertRedirect(route('unit-activities.index', 'humas-marketing'));
        $this->assertDatabaseHas('unit_activities', ['id' => $activity->id, 'judul' => 'CRUD Unit Updated', 'status' => 'Selesai']);
        $this->actingAs($admin)->delete(route('unit-activities.destroy', ['humas-marketing', $activity]))->assertRedirect();
        $this->assertDatabaseMissing('unit_activities', ['id' => $activity->id]);

        $this->actingAs($admin)->post(route('press-releases.store'), ['title' => 'CRUD Berita', 'excerpt' => 'Ringkas', 'content' => '<p>Isi</p>', 'status' => 'Draft'])->assertRedirect(route('publications.index', 'berita'));
        $press = PressRelease::where('title', 'CRUD Berita')->firstOrFail();
        $this->actingAs($admin)->put(route('press-releases.update', $press), ['title' => 'CRUD Berita Updated', 'excerpt' => 'Ringkas', 'content' => '<p>Isi update</p>', 'status' => 'Published'])->assertRedirect();
        $this->assertDatabaseHas('press_releases', ['id' => $press->id, 'title' => 'CRUD Berita Updated', 'status' => 'Published']);
        $this->actingAs($admin)->delete(route('press-releases.destroy', $press))->assertRedirect();
        $this->assertDatabaseMissing('press_releases', ['id' => $press->id]);

        $this->actingAs($admin)->post(route('careers.store'), ['type' => 'Loker', 'title' => 'CRUD Karir', 'company' => 'UBP', 'external_url' => 'https://example.com', 'status' => 'Draft'])->assertRedirect();
        $career = CareerPost::where('title', 'CRUD Karir')->firstOrFail();
        $this->actingAs($admin)->put(route('careers.update', $career), ['type' => 'Job Fair', 'title' => 'CRUD Karir Updated', 'company' => 'UBP', 'external_url' => 'https://example.com/job', 'status' => 'Published'])->assertRedirect();
        $this->assertDatabaseHas('career_posts', ['id' => $career->id, 'title' => 'CRUD Karir Updated', 'status' => 'Published']);
        $this->actingAs($admin)->delete(route('careers.destroy', $career))->assertRedirect();
        $this->assertDatabaseMissing('career_posts', ['id' => $career->id]);

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->actingAs($super)->post(route('users.store'), ['name' => 'CRUD User', 'email' => 'crud-user@example.test', 'password' => 'password123', 'role' => 'admin'])->assertRedirect();
        $user = User::where('email', 'crud-user@example.test')->firstOrFail();
        $this->actingAs($super)->put(route('users.update', $user), ['name' => 'CRUD User Updated', 'email' => 'crud-user-updated@example.test', 'password' => '', 'role' => 'admin'])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'CRUD User Updated', 'email' => 'crud-user-updated@example.test']);
        $this->actingAs($super)->delete(route('users.destroy', $user))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    private function adminUser(): User
    {
        return User::where('email', 'admin@ubpkarawang.ac.id')->firstOrFail();
    }
}
