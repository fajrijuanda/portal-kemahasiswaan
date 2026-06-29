<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'tahun_akademik', 'periode', 'is_active'])]
class Semester extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
