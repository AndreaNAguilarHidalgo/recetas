<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    // Campos que se agregaran
    protected $fillable = [
        'titulo', 'preparacion', 'ingredientes', 'imagen', 'categoria_id'
    ];

    //Obtenr la categoria por medio de FK
    public function  categoria()
    {
        return $this->belongsTo(CategoriaReceta::class);
    }

    // Obtener la información del usuario vía FK
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Likes que ha recibido una receta 
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes_receta');
    }
}
