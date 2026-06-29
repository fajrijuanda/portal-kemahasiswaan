<?php

namespace Database\Seeders;

use App\Models\Beasiswa;
use App\Models\ClaimFasilitas;
use App\Models\ClaimTransport;
use App\Models\Event;
use App\Models\Prestasi;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\TracerStudy;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect(['super user', 'admin', 'kaprodi', 'kabag', 'warek'])->map(fn ($role) => Role::firstOrCreate(['name' => $role]));

        $prodis = collect([
            ['nama' => 'Manajemen', 'kode' => 'MNJ', 'fakultas' => 'Ekonomi dan Bisnis'],
            ['nama' => 'Akuntansi', 'kode' => 'AKT', 'fakultas' => 'Ekonomi dan Bisnis'],
            ['nama' => 'Teknik Informatika', 'kode' => 'TI', 'fakultas' => 'Ilmu Komputer'],
            ['nama' => 'Sistem Informasi', 'kode' => 'SI', 'fakultas' => 'Ilmu Komputer'],
            ['nama' => 'Ilmu Hukum', 'kode' => 'HKM', 'fakultas' => 'Hukum'],
            ['nama' => 'Pendidikan Guru SD', 'kode' => 'PGSD', 'fakultas' => 'Keguruan dan Ilmu Pendidikan'],
        ])->map(fn ($data) => Prodi::create($data));

        $ganjil = Semester::create(['nama' => 'Ganjil 2026/2027', 'tahun_akademik' => '2026/2027', 'periode' => 'Ganjil', 'is_active' => true]);
        $genap = Semester::create(['nama' => 'Genap 2026/2027', 'tahun_akademik' => '2026/2027', 'periode' => 'Genap']);

        $superUser = User::create([
            'name' => 'Super User',
            'email' => 'super@ubpkarawang.ac.id',
            'password' => Hash::make('password'),
        ])->assignRole('super user');

        $admin = User::create([
            'name' => 'Admin Kemahasiswaan',
            'email' => 'admin@ubpkarawang.ac.id',
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        User::create([
            'name' => 'Warek',
            'email' => 'warek@ubpkarawang.ac.id',
            'password' => Hash::make('password'),
        ])->assignRole('warek');

        User::create([
            'name' => 'Kabag Kemahasiswaan',
            'email' => 'kabag@ubpkarawang.ac.id',
            'password' => Hash::make('password'),
        ])->assignRole('kabag');

        $prodis->take(3)->each(function (Prodi $prodi) {
            User::create([
                'name' => 'Kaprodi '.$prodi->nama,
                'email' => strtolower($prodi->kode).'@ubpkarawang.ac.id',
                'password' => Hash::make('password'),
                'prodi_id' => $prodi->id,
            ])->assignRole('kaprodi');
        });

        foreach ($prodis as $index => $prodi) {
            Prestasi::create([
                'semester_id' => $index % 2 === 0 ? $ganjil->id : $genap->id,
                'prodi_id' => $prodi->id,
                'nama_mahasiswa' => 'Mahasiswa '.$prodi->kode,
                'nim' => '2026'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'nama_kegiatan' => 'Lomba Inovasi Mahasiswa',
                'tingkat' => ['Regional', 'Nasional', 'Internasional'][$index % 3],
                'peringkat' => 'Juara '.(($index % 3) + 1),
                'penyelenggara' => 'UBP Karawang',
                'tanggal' => now()->subDays($index + 1),
                'status' => 'Terverifikasi',
                'created_by' => $admin->id,
            ]);

            Event::create([
                'semester_id' => $ganjil->id,
                'prodi_id' => $prodi->id,
                'nama_pengaju' => 'Delegasi Event '.$prodi->kode,
                'nim' => '2026'.str_pad((string) ($index + 60), 4, '0', STR_PAD_LEFT),
                'jenis_reimbursement' => ['Akomodasi', 'Pendaftaran', 'Transport', 'Fasilitas', 'Lainnya'][$index % 5],
                'nama_kegiatan' => 'Kegiatan Nasional Mahasiswa',
                'tanggal' => now()->subDays($index + 12),
                'nominal' => 500000 + ($index * 75000),
                'status' => $index % 2 === 0 ? 'Disetujui' : 'Diajukan',
                'created_by' => $superUser->id,
            ]);

            ClaimTransport::create([
                'semester_id' => $ganjil->id,
                'prodi_id' => $prodi->id,
                'nama_mahasiswa' => 'Delegasi '.$prodi->kode,
                'nim' => '2026'.str_pad((string) ($index + 20), 4, '0', STR_PAD_LEFT),
                'kegiatan' => 'Seminar Nasional',
                'tujuan' => 'Karawang',
                'tanggal' => now()->subDays($index + 5),
                'nominal' => 250000 + ($index * 50000),
                'status' => $index % 2 === 0 ? 'Disetujui' : 'Diajukan',
                'created_by' => $admin->id,
            ]);

            ClaimFasilitas::create([
                'semester_id' => $ganjil->id,
                'prodi_id' => $prodi->id,
                'nama_pengaju' => 'Himpunan '.$prodi->kode,
                'fasilitas' => 'Aula',
                'keperluan' => 'Kegiatan mahasiswa',
                'tanggal' => now()->subDays($index + 10),
                'jumlah' => 1,
                'status' => 'Disetujui',
                'created_by' => $admin->id,
            ]);

            TracerStudy::create([
                'semester_id' => $ganjil->id,
                'prodi_id' => $prodi->id,
                'jumlah_mahasiswa' => 80 + ($index * 8),
                'jumlah_input' => 55 + ($index * 6),
                'periode_yudisium' => 'Yudisium 2026',
                'status' => 'Berjalan',
                'created_by' => $admin->id,
            ]);

            Beasiswa::create([
                'semester_id' => $genap->id,
                'prodi_id' => $prodi->id,
                'nama_mahasiswa' => 'Penerima Beasiswa '.$prodi->kode,
                'nim' => '2026'.str_pad((string) ($index + 40), 4, '0', STR_PAD_LEFT),
                'jenis_beasiswa' => $index % 2 === 0 ? 'KIP Kuliah' : 'Prestasi',
                'sumber' => 'Internal/External',
                'nominal' => 3000000,
                'status' => 'Aktif',
                'created_by' => $admin->id,
            ]);
        }
    }
}
