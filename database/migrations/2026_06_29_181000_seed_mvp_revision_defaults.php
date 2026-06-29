<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['mahasiswa', 'ormawa'] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach (['KIP', 'Kacer', 'Tahfidz', 'Lainnya'] as $type) {
            DB::table('scholarship_types')->updateOrInsert(
                ['nama' => $type],
                ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach (range(1, 23) as $index) {
            DB::table('competitions')->updateOrInsert(
                ['nama' => 'Lomba Prestasi '.$index],
                ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('competitions')->whereIn('nama', collect(range(1, 23))->map(fn ($index) => 'Lomba Prestasi '.$index)->all())->delete();
        DB::table('scholarship_types')->whereIn('nama', ['KIP', 'Kacer', 'Tahfidz', 'Lainnya'])->delete();
        DB::table('roles')->whereIn('name', ['mahasiswa', 'ormawa'])->delete();
    }
};
