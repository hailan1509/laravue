<?php

namespace App\Laravue\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

/**

 */
class ChiTietHoaDon extends Model
{
    protected $table = 'chi_tiet_hoa_don';
    protected $fillable = [
        'ma_hoa_don',
        'ma_dich_vu',
        'soluong',
        'ten_dich_vu',
        'gia',
        'gia_khuyen_mai',
    ];
}
