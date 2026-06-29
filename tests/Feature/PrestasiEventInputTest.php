<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrestasiEventInputTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_submit_prestasi_with_photo_and_publication_link(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::where('email', 'admin@ubpkarawang.ac.id')->firstOrFail();
        $semester = Semester::firstOrFail();
        $prodi = Prodi::firstOrFail();

        $this->actingAs($admin)->post('/prestasi', [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'nama_mahasiswa' => 'Raka Prestasi',
            'nim' => '20260001',
            'nama_kegiatan' => 'Kompetisi Nasional',
            'tingkat' => 'Nasional',
            'peringkat' => 'Juara 1',
            'penyelenggara' => 'Panitia Nasional',
            'tanggal' => '2026-06-19',
            'foto_path' => UploadedFile::fake()->image('prestasi.jpg'),
            'publikasi_url' => 'https://ubpkarawang.ac.id/prestasi/raka',
            'status' => 'Draft',
        ])->assertRedirect('/records/prestasi');

        $prestasi = Prestasi::where('nama_mahasiswa', 'Raka Prestasi')->firstOrFail();

        $this->assertSame('Nasional', $prestasi->tingkat);
        $this->assertSame('https://ubpkarawang.ac.id/prestasi/raka', $prestasi->publikasi_url);
        Storage::disk('public')->assertExists($prestasi->foto_path);
    }

    public function test_admin_can_submit_event_reimbursement_with_evidence(): void
    {
        Storage::fake('public');
        $this->seed();

        $admin = User::where('email', 'admin@ubpkarawang.ac.id')->firstOrFail();
        $semester = Semester::firstOrFail();
        $prodi = Prodi::firstOrFail();

        $this->actingAs($admin)->post('/event', [
            'semester_id' => $semester->id,
            'prodi_id' => $prodi->id,
            'nama_pengaju' => 'Nadia Event',
            'nim' => '20260002',
            'jenis_reimbursement' => 'Akomodasi',
            'nama_kegiatan' => 'Seminar Nasional',
            'tanggal' => '2026-06-19',
            'nominal' => 750000,
            'bukti_path' => UploadedFile::fake()->image('bukti.jpg'),
            'status' => 'Diajukan',
        ])->assertRedirect('/records/event');

        $event = Event::where('nama_pengaju', 'Nadia Event')->firstOrFail();

        $this->assertSame('Akomodasi', $event->jenis_reimbursement);
        Storage::disk('public')->assertExists($event->bukti_path);
    }
}
