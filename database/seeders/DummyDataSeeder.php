<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitActivity;
use App\Models\OrmawaProposal;
use App\Models\User;
use App\Models\Semester;
use App\Models\Prodi;
use App\Models\Ormawa;
use App\Models\PressRelease;
use App\Models\CareerPost;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $user = User::first();
        $semester = Semester::first();
        $prodi = Prodi::first();
        $ormawa = Ormawa::first();

        if (!$user || !$semester || !$prodi) {
            return;
        }

        $units = ['humas-marketing', 'science-center', 'pengembangan-ormawa', 'alumni-pusat-karir'];
        $statuses = ['Draft', 'Berjalan', 'Selesai', 'Tertunda'];

        foreach ($units as $unit) {
            if (UnitActivity::where('unit', $unit)->count() < 3) {
                for ($i = 0; $i < 3; $i++) {
                    UnitActivity::create([
                        'unit' => $unit,
                        'ormawa_id' => ($unit === 'pengembangan-ormawa' && $ormawa) ? $ormawa->id : null,
                        'semester_id' => $semester->id,
                        'prodi_id' => $prodi->id,
                        'judul' => $faker->sentence(4),
                        'penanggung_jawab' => $faker->name(),
                        'tanggal' => tap($faker->dateTimeBetween('-2 months', '+1 month'))->setTime(0, 0, 0),
                        'status' => $faker->randomElement($statuses),
                        'catatan' => $faker->paragraph(),
                        'created_by' => $user->id,
                    ]);
                }
            }
        }

        $proposalStatuses = ['Diajukan', 'Diproses', 'Revisi', 'Disetujui', 'Ditolak'];
        if ($ormawa && OrmawaProposal::count() < 5) {
            for ($i = 0; $i < 5; $i++) {
                OrmawaProposal::create([
                    'ormawa_id' => $ormawa->id,
                    'semester_id' => $semester->id,
                    'judul' => "Proposal " . $faker->words(3, true),
                    'tanggal' => tap($faker->dateTimeBetween('now', '+2 months'))->setTime(0, 0, 0),
                    'lokasi' => $faker->address(),
                    'deskripsi' => $faker->text(),
                    'proposal_path' => null,
                    'status' => $faker->randomElement($proposalStatuses),
                    'catatan' => $faker->sentence(),
                    'created_by' => $user->id,
                ]);
            }
        }

        $ormawaNames = ['BEM Fakultas Ilmu Komputer', 'HIMA Sistem Informasi', 'HIMA Teknik Informatika', 'UKM Basket', 'UKM Futsal'];
        foreach ($ormawaNames as $name) {
            if (Ormawa::where('nama', $name)->count() == 0) {
                Ormawa::create([
                    'user_id' => $user->id,
                    'nama' => $name,
                    'jenis' => str_contains($name, 'UKM') ? 'UKM' : 'BEM/HIMA',
                    'pembina' => $faker->name() . ', S.Kom., M.Kom.',
                    'kontak' => $faker->phoneNumber(),
                    'deskripsi' => $faker->sentence(),
                    'status' => 'Aktif',
                ]);
            }
        }

        if (PressRelease::count() < 5) {
            for ($i = 0; $i < 5; $i++) {
                PressRelease::create([
                    'title' => rtrim($faker->sentence(6), '.'),
                    'slug' => \Illuminate\Support\Str::slug($faker->sentence(4)) . '-' . uniqid(),
                    'content' => $faker->paragraphs(3, true),
                    'published_at' => tap($faker->dateTimeBetween('-1 month', 'now'))->setTime(0, 0, 0),
                    'status' => 'Published',
                    'created_by' => $user->id,
                ]);
            }
        }

        $types = ['Lowongan Kerja', 'Magang', 'Job Fair'];
        if (CareerPost::count() < 5) {
            for ($i = 0; $i < 5; $i++) {
                CareerPost::create([
                    'title' => $faker->jobTitle(),
                    'company' => $faker->company(),
                    'location' => $faker->city(),
                    'type' => $faker->randomElement($types),
                    'content' => $faker->paragraphs(2, true) . "\n\n1. Requirement 1\n2. Requirement 2",
                    'deadline' => tap($faker->dateTimeBetween('now', '+2 months'))->setTime(0, 0, 0),
                    'external_url' => $faker->url(),
                    'status' => 'Published',
                    'created_by' => $user->id,
                ]);
            }
        }
    }
}
