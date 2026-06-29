<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->after('prodi_id');
            }
        });

        Schema::create('scholarship_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ormawas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama')->unique();
            $table->string('jenis')->nullable();
            $table->string('pembina')->nullable();
            $table->string('kontak')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });

        Schema::create('achievement_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prodi_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('slot_prestasi')->default(0);
            $table->unsignedInteger('terpakai')->default(0);
            $table->timestamps();
            $table->unique(['semester_id', 'prodi_id']);
        });

        Schema::table('prestasis', function (Blueprint $table) {
            if (! Schema::hasColumn('prestasis', 'competition_id')) {
                $table->foreignId('competition_id')->nullable()->after('prodi_id')->constrained('competitions')->nullOnDelete();
            }
            if (! Schema::hasColumn('prestasis', 'kategori_event')) {
                $table->string('kategori_event')->nullable()->after('nama_kegiatan');
            }
            if (! Schema::hasColumn('prestasis', 'scope')) {
                $table->string('scope')->nullable()->after('kategori_event');
            }
            if (! Schema::hasColumn('prestasis', 'juara')) {
                $table->string('juara')->nullable()->after('scope');
            }
        });

        Schema::table('beasiswas', function (Blueprint $table) {
            if (! Schema::hasColumn('beasiswas', 'scholarship_type_id')) {
                $table->foreignId('scholarship_type_id')->nullable()->after('prodi_id')->constrained('scholarship_types')->nullOnDelete();
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'ormawa_id')) {
                $table->foreignId('ormawa_id')->nullable()->after('prodi_id')->constrained('ormawas')->nullOnDelete();
            }
            if (! Schema::hasColumn('events', 'foto_path')) {
                $table->string('foto_path')->nullable()->after('bukti_path');
            }
            if (! Schema::hasColumn('events', 'surat_tugas_path')) {
                $table->string('surat_tugas_path')->nullable()->after('foto_path');
            }
            if (! Schema::hasColumn('events', 'sertifikat_path')) {
                $table->string('sertifikat_path')->nullable()->after('surat_tugas_path');
            }
            if (! Schema::hasColumn('events', 'link_penyelenggara')) {
                $table->string('link_penyelenggara')->nullable()->after('sertifikat_path');
            }
        });

        Schema::table('unit_activities', function (Blueprint $table) {
            if (! Schema::hasColumn('unit_activities', 'ormawa_id')) {
                $table->foreignId('ormawa_id')->nullable()->after('unit')->constrained('ormawas')->nullOnDelete();
            }
        });

        Schema::create('ormawa_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ormawa_id')->constrained('ormawas')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->date('tanggal')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('proposal_path')->nullable();
            $table->string('status')->default('Diajukan');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('press_releases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('career_posts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->date('deadline')->nullable();
            $table->string('external_url')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_posts');
        Schema::dropIfExists('press_releases');
        Schema::dropIfExists('ormawa_proposals');

        Schema::table('events', function (Blueprint $table) {
            foreach (['link_penyelenggara', 'sertifikat_path', 'surat_tugas_path', 'foto_path'] as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
            if (Schema::hasColumn('events', 'ormawa_id')) {
                $table->dropConstrainedForeignId('ormawa_id');
            }
        });

        Schema::table('unit_activities', function (Blueprint $table) {
            if (Schema::hasColumn('unit_activities', 'ormawa_id')) {
                $table->dropConstrainedForeignId('ormawa_id');
            }
        });

        Schema::table('beasiswas', function (Blueprint $table) {
            if (Schema::hasColumn('beasiswas', 'scholarship_type_id')) {
                $table->dropConstrainedForeignId('scholarship_type_id');
            }
        });

        Schema::table('prestasis', function (Blueprint $table) {
            foreach (['juara', 'scope', 'kategori_event'] as $column) {
                if (Schema::hasColumn('prestasis', $column)) {
                    $table->dropColumn($column);
                }
            }
            if (Schema::hasColumn('prestasis', 'competition_id')) {
                $table->dropConstrainedForeignId('competition_id');
            }
        });

        Schema::dropIfExists('achievement_quotas');
        Schema::dropIfExists('ormawas');
        Schema::dropIfExists('competitions');
        Schema::dropIfExists('scholarship_types');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nim')) {
                $table->dropColumn('nim');
            }
        });
    }
};
