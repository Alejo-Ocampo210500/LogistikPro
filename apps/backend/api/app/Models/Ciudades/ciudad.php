<?php

namespace App\Models\Ciudades;

use App\Models\Departamentos\Departamento;
use App\Models\Estados\Estado;
use App\Models\Provedores\provedor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ciudad extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'codigo',
        'departamento_id',
        'estado_id',
        'creado_por',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function proveedores()
    {
        return $this->hasMany(provedor::class, 'ciudad_id');
    }
}
