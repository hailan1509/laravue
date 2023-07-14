<?php

namespace App\Laravue\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

/**

 */
class ChiTietCombo extends Model
{
    protected $table = 'chi_tiet_combo';
    protected $fillable = [
        'ma_dich_vu',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
