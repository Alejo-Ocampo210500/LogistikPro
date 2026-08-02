<?php

namespace App\Models\Seguridad;

use App\Models\User;
use App\Models\Estados\Estado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rol extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado_id'
    ];



    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }   
}
