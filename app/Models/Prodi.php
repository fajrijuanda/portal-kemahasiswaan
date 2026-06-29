<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'kode', 'fakultas'])]
class Prodi extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
