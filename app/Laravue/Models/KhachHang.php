<?php

namespace App\Laravue\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

/**

 */
class KhachHang extends Model
{
    protected $table = 'khach_hang';
    protected $fillable = [
        'ten',
        'sdt',
    ];
}
