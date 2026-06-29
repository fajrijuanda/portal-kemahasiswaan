<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'jumlah_mahasiswa', 'jumlah_input', 'periode_yudisium', 'status', 'catatan', 'created_by'])]
class TracerStudy extends Model
{
    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
