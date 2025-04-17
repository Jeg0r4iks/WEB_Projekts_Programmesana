<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // подключи, если используешь Sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Поля, разрешённые для массового заполнения.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * Поля, скрытые при сериализации (например, в JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Преобразование типов (кастинг).
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 10+ автоматически хеширует
        ];
    }
}
