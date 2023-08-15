<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Класс для авторизации админов
 * Class AdminModel
 * @package App\Models\Auth
 */
class AdminModel extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $connection = 'main';
    const SUPER_ADMIN_ROLE = 'superadmin';
    const ADMIN_ROLE = 'admin';

    const ROLES = [
      self::SUPER_ADMIN_ROLE,
      self::ADMIN_ROLE,
    ];

    protected $table = 'admins';

    public $hidden = [
        'password',
    ];

    public $fillable = [
        'name',
        'email',
        'role',
    ];
}
