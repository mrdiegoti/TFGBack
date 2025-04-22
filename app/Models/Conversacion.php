<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 'descripcion',
    ];

    public function commentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function foro()
    {
        return $this->belongsTo(Foro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
