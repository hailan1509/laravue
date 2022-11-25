<?php

namespace App\Laravue\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

/**

 */
class HopDong extends Model
{
    protected $table = 'nguoi_thue';
}
