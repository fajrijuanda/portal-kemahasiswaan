<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['semester_id', 'prodi_id', 'slot_prestasi', 'terpakai'])]
class AchievementQuota extends Model
{
    public function semester() { return $this->belongsTo(Semester::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }
}
