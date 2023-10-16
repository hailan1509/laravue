<?php

namespace App\Laravue\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

/**

 */
class HoaDon extends Model
{
    protected $table = 'hoa_don';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'ten_khach_hang',
        'sdt',
        'chuyen_khoan',
        'tong_tien',
        'ngay',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}
