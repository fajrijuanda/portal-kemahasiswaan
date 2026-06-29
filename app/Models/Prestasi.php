<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'competition_id', 'nama_mahasiswa', 'nim', 'nama_kegiatan', 'kategori_event', 'scope', 'juara', 'tingkat', 'peringkat', 'penyelenggara', 'tanggal', 'status', 'catatan', 'foto_path', 'publikasi_url', 'created_by'])]
class Prestasi extends Model
{
    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function competition() { return $this->belongsTo(Competition::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
