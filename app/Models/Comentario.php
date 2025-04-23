<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'texto', 'user_id', 'conversacion_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class);
    }
}
