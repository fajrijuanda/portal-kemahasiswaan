<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'scholarship_type_id', 'nama_mahasiswa', 'nim', 'jenis_beasiswa', 'sumber', 'nominal', 'status', 'catatan', 'created_by'])]
class Beasiswa extends Model
{
    protected function casts(): array
    {
        return ['nominal' => 'decimal:2'];
    }

    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function scholarshipType() { return $this->belongsTo(ScholarshipType::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
