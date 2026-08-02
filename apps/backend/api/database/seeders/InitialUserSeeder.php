<?php

namespace Database\Seeders;

use App\Models\Empresas\Empresa;
use App\Models\Planes\Plan;
use App\Models\Seguridad\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialUserSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('estados')->count() === 0) {
            $this->call(EstadoSeeder::class);
        }

        $estadoActivo = DB::table('estados')->where('nombre', 'Activo')->first();
        if (!$estadoActivo) {
            $estadoActivoId = DB::table('estados')->insertGetId([
                'nombre' => 'Activo',
                'descripcion' => 'Estado Activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $estadoActivoId = $estadoActivo->id;
        }

        $rolSuperadmin = Rol::updateOrCreate(
            ['nombre' => 'Superadministrador'],
            [
                'descripcion' => 'Dueño y administrador de la plataforma',
                'estado_id' => $estadoActivoId,
            ]
        );

        $rolAdministrador = Rol::updateOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Administrador del sistema',
                'estado_id' => $estadoActivoId,
            ]
        );

        // Seed Plans
        $planBasico = Plan::updateOrCreate(
            ['nombre' => 'basico'],
            [
                'descripcion' => 'Plan Básico',
                'precio' => 39900,
                'duracion_meses' => 1,
                'estado_id' => $estadoActivoId,
            ]
        );

        $empresaCentral = Empresa::updateOrCreate(
            ['nit' => '9000000001'],
            [
                'nombre_comercial' => 'LogistikPro Central',
                'razon_social' => 'LogistikPro Central SAS',
                'email' => 'superadmin@logistikpro.com',
                'telefono' => '3000000000',
                'direccion' => 'Medellín',
                'ciudad' => 'Medellín',
                'departamento' => 'Antioquia',
                'estado_id' => $estadoActivoId,
                'plan' => $planBasico->nombre,
                'plan_id' => $planBasico->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@logistikpro.com'],
            [
                'empresa_id' => $empresaCentral->id,
                'rol_id' => $rolSuperadmin->id,
                'nombre' => 'Alejandro',
                'apellido' => 'Ocampo',
                'telefono' => '3000000000',
                'password' => '12345678',
                'estado_id' => $estadoActivoId,
            ]
        );
    }
}
