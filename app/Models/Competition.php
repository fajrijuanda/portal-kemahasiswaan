<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'is_active'])]
class Competition extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
