<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->string('fakultas')->nullable();
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tahun_akademik');
            $table->enum('periode', ['Ganjil', 'Genap']);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('prodi_id')->nullable()->after('email')->constrained('prodis')->nullOnDelete();
        });

        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_mahasiswa');
            $table->string('nim')->nullable();
            $table->string('nama_kegiatan');
            $table->string('tingkat')->nullable();
            $table->string('peringkat')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('status')->default('Draft');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('claim_transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_mahasiswa');
            $table->string('nim')->nullable();
            $table->string('kegiatan');
            $table->string('tujuan')->nullable();
            $table->date('tanggal')->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->string('status')->default('Diajukan');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('claim_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pengaju');
            $table->string('fasilitas');
            $table->string('keperluan')->nullable();
            $table->date('tanggal')->nullable();
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('status')->default('Diajukan');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('jumlah_mahasiswa')->default(0);
            $table->unsignedInteger('jumlah_input')->default(0);
            $table->string('periode_yudisium')->nullable();
            $table->string('status')->default('Berjalan');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('beasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->string('nama_mahasiswa');
            $table->string('nim')->nullable();
            $table->string('jenis_beasiswa');
            $table->string('sumber')->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->string('status')->default('Aktif');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beasiswas');
        Schema::dropIfExists('tracer_studies');
        Schema::dropIfExists('claim_fasilitas');
        Schema::dropIfExists('claim_transports');
        Schema::dropIfExists('prestasis');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prodi_id');
        });
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('prodis');
    }
};
