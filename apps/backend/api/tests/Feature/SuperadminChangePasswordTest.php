<?php

namespace Tests\Feature;

use App\Models\Empresas\Empresa;
use App\Models\Planes\Plan;
use App\Models\Seguridad\Rol;
use App\Models\User;
use App\Models\Estados\Estado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperadminChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;
    protected User $companyAdmin;
    protected Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        // Create States
        $estadoActivo = Estado::create([
            'nombre' => 'Activo'
        ]);

        $estadoActivoId = $estadoActivo->id;

        // Create Roles
        $rolSuperadmin = Rol::create([
            'nombre' => 'Superadministrador',
            'descripcion' => 'Superadmin descriptor',
            'estado_id' => $estadoActivoId,
        ]);

        $rolAdministrador = Rol::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Admin descriptor',
            'estado_id' => $estadoActivoId,
        ]);

        // Create Plans
        $planPlataforma = Plan::create([
            'nombre' => 'plataforma',
            'descripcion' => 'Plan Plataforma',
            'precio' => 0.00,
            'duracion_meses' => 12,
            'estado_id' => $estadoActivoId,
        ]);

        $planBasico = Plan::create([
            'nombre' => 'basico',
            'descripcion' => 'Plan Basico',
            'precio' => 9.99,
            'duracion_meses' => 1,
            'estado_id' => $estadoActivoId,
        ]);

        // Create Companies
        $empresaCentral = Empresa::create([
            'nombre_comercial' => 'LogistikPro Central',
            'razon_social' => 'LogistikPro Central SAS',
            'nit' => '9000000001',
            'email' => 'superadmin@logistikpro.com',
            'estado_id' => $estadoActivoId,
            'plan' => 'plataforma',
            'plan_id' => $planPlataforma->id,
        ]);

        $this->empresa = Empresa::create([
            'nombre_comercial' => 'Empresa Test',
            'razon_social' => 'Empresa Test SAS',
            'nit' => '9001234567',
            'email' => 'test@empresa.com',
            'estado_id' => $estadoActivoId,
            'plan' => 'basico',
            'plan_id' => $planBasico->id,
        ]);

        // Create Users
        $this->superadmin = User::create([
            'empresa_id' => $empresaCentral->id,
            'rol_id' => $rolSuperadmin->id,
            'nombre' => 'Super',
            'apellido' => 'Admin',
            'email' => 'superadmin@logistikpro.com',
            'password' => '12345678',
            'estado_id' => $estadoActivoId,
        ]);

        $this->companyAdmin = User::create([
            'empresa_id' => $this->empresa->id,
            'rol_id' => $rolAdministrador->id,
            'nombre' => 'Admin',
            'apellido' => 'Empresa',
            'email' => 'admin@empresa.com',
            'password' => 'OldPassword123',
            'estado_id' => $estadoActivoId,
        ]);
    }

    public function test_superadmin_can_change_company_password_with_valid_password(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->putJson("/api/superadmin/empresas/{$this->empresa->id}/cambiar-password", [
                'password' => 'NewPassword123'
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'mensaje' => 'Contraseña del administrador actualizada correctamente.',
            'usuario_email' => $this->companyAdmin->email
        ]);

        // Verify the database password has been updated and hashed
        $this->companyAdmin->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $this->companyAdmin->password));
    }

    public function test_it_validates_password_requirements(): void
    {
        // 1. Weak password (too short)
        $response = $this->actingAs($this->superadmin)
            ->putJson("/api/superadmin/empresas/{$this->empresa->id}/cambiar-password", [
                'password' => 'Sh1'
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);

        // 2. Missing uppercase
        $response = $this->actingAs($this->superadmin)
            ->putJson("/api/superadmin/empresas/{$this->empresa->id}/cambiar-password", [
                'password' => 'weakpassword123'
            ]);

        $response->assertStatus(422);

        // 3. Missing lowercase
        $response = $this->actingAs($this->superadmin)
            ->putJson("/api/superadmin/empresas/{$this->empresa->id}/cambiar-password", [
                'password' => 'WEAKPASSWORD123'
            ]);

        $response->assertStatus(422);

        // 4. Missing number
        $response = $this->actingAs($this->superadmin)
            ->putJson("/api/superadmin/empresas/{$this->empresa->id}/cambiar-password", [
                'password' => 'WeakPasswordNoNumber'
            ]);

        $response->assertStatus(422);
    }

    public function test_non_superadmin_cannot_change_password(): void
    {
        $response = $this->actingAs($this->companyAdmin)
            ->putJson("/api/superadmin/empresas/{$this->empresa->id}/cambiar-password", [
                'password' => 'NewPassword123'
            ]);

        $response->assertStatus(403);
    }
}
