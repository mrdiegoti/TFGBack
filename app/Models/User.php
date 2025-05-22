<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Atributos ocultos al serializar.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos con casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Puedes cambiarlo a 'string' si no usas Laravel 10+
    ];

    /**
     * Obtiene el identificador que se almacenará en el token JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Devuelve un array de claims personalizados para el JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function conversacion()
    {
        return $this->hasMany(Conversacion::class);
    }
    
    public function comentario()
    {
        return $this->hasMany(Comentario::class);
    }

    public function gameComments()
{
    return $this->hasMany(GameComment::class);
}

}
