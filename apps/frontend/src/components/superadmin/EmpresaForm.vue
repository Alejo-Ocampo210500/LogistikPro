<template>
  <section class="form-card">
    <div class="form-header">
      <div>
        <span class="form-kicker">{{ isEditMode ? 'Editar empresa' : 'Nueva empresa' }}</span>
        <h2>{{ isEditMode ? 'Actualizar empresa' : 'Crear empresa y administrador' }}</h2>
      </div>
    </div>

    <form class="company-form" @submit.prevent="attemptSubmit">
      <div v-if="validationMessage" class="flash error">
        {{ validationMessage }}
      </div>

      <div class="grid grid-2">
        <label class="field">
          <span>Nombre comercial</span>
          <input v-model.trim="form.nombre_comercial" type="text" placeholder="LogistikPro Central" />
        </label>

        <label class="field">
          <span>Razón social</span>
          <input v-model.trim="form.razon_social" type="text" placeholder="LogistikPro Central SAS" />
        </label>
      </div>

      <div class="grid grid-2">
        <label class="field">
          <span>NIT</span>
          <input v-model.trim="form.nit" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="900000123" />
        </label>

        <label class="field">
          <span>Teléfono</span>
          <input v-model.trim="form.telefono" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="3000000000" />
        </label>
      </div>

      <div class="grid grid-1">
        <label class="field">
          <span>Correo de la empresa</span>
          <input v-model.trim="form.email" type="email" placeholder="contacto@empresa.com" />
        </label>
      </div>

      <div :class="['grid', isEditMode ? 'grid-2' : 'grid-1']">
        <label v-if="isEditMode" class="field">
          <span>Estado</span>
          <select v-model.number="form.estado_id">
            <option v-for="est in estados" :key="est.id" :value="est.id">
              {{ est.nombre }}
            </option>
          </select>
        </label>

        <label class="field">
          <span>Logo</span>
          <input v-model.trim="form.logo" type="text" placeholder="https://..." />
        </label>
      </div>

      <label class="field">
        <span>Dirección</span>
        <input v-model.trim="form.direccion" type="text" placeholder="Dirección física de la empresa" />
      </label>

      <div class="grid grid-2">
        <label class="field">
          <span>Departamento</span>
          <select v-model.number="form.departamento_id">
            <option :value="null">Selecciona un departamento</option>
            <option v-for="departamento in departamentosDisponibles" :key="departamento.id" :value="departamento.id">
              {{ departamento.nombre }}
            </option>
          </select>
        </label>

        <label class="field">
          <span>Ciudad</span>
          <select v-model.number="form.ciudad_id" :disabled="!form.departamento_id">
            <option :value="null">Selecciona una ciudad</option>
            <option v-for="ciudad in ciudadesDisponibles" :key="ciudad.id" :value="ciudad.id">
              {{ ciudad.nombre }}
            </option>
          </select>
        </label>
      </div>

      <div v-if="!isEditMode" class="separator">
        <span>Administrador inicial</span>
      </div>

      <div v-if="!isEditMode" class="grid grid-2">
        <label class="field">
          <span>Nombres</span>
          <input v-model.trim="form.admin_nombre" type="text" placeholder="Nombre del administrador" />
        </label>

        <label class="field">
          <span>Apellidos</span>
          <input v-model.trim="form.admin_apellido" type="text" placeholder="Apellido del administrador" />
        </label>
      </div>

      <div v-if="!isEditMode" class="grid grid-2">
        <label class="field">
          <span>Usuario del administrador</span>
          <div class="admin-email-row">
            <input v-model.trim="form.admin_email_user" type="text" placeholder="usuario" />
            <span class="email-domain">@LOGISTIKPRO.COM</span>
          </div>
          <small class="field-note">El sistema completa el dominio automáticamente.</small>
        </label>

        <label class="field">
          <span>Teléfono del administrador</span>
          <input v-model.trim="form.admin_telefono" type="tel" inputmode="numeric" data-only-numeric="true" placeholder="3000000000" />
        </label>
      </div>

      <label v-if="!isEditMode" class="field">
        <span>Contraseña inicial</span>
        <div class="password-wrap">
          <input
            v-model="form.admin_password"
            :type="showAdminPassword ? 'text' : 'password'"
            placeholder="Mínimo 8 caracteres"
          />
          <button
            type="button"
            class="ghost-button"
            @click="showAdminPassword = !showAdminPassword"
            :aria-label="showAdminPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
          >
            <i :class="showAdminPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
          </button>
        </div>
      </label>

      <div class="actions">
        <button class="submit-button" type="submit" :disabled="loading">
          {{ loading ? (isEditMode ? 'Actualizando empresa...' : 'Creando empresa...') : (isEditMode ? 'Actualizar empresa' : 'Crear empresa') }}
        </button>
      </div>
    </form>
  </section>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'EmpresaForm',

  props: {
    mode: {
      type: String,
      default: 'crear',
    },
    empresa: {
      type: Object,
      default: null,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    planes: {
      type: Array,
      default: () => [],
    },
    estados: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      form: {
        id: null,
        nombre_comercial: '',
        razon_social: '',
        nit: '',
        email: '',
        telefono: '',
        direccion: '',
        ciudad_id: null,
        departamento_id: null,
        ciudad: '',
        departamento: '',
        logo: '',
        estado_id: null,
        admin_nombre: '',
        admin_apellido: '',
        admin_email_user: '',
        admin_email: '',
        admin_telefono: '',
        admin_password: '',
      },
      showAdminPassword: false,
      departamentos: [],
      ciudades: [],
      validationMessage: '',
      validationTimer: null,
    };
  },

  computed: {
    isEditMode() {
      return this.mode === 'editar';
    },

    ciudadesDisponibles() {
      const departamentoId = Number(this.form.departamento_id || 0);

      if (!departamentoId) {
        return [];
      }

      return this.ciudades.filter((item) => Number(item.departamento_id) === departamentoId);
    },

    departamentosDisponibles() {
      return this.departamentos;
    },
  },

  watch: {
    empresa: {
      immediate: true,
      handler(value) {
        if (value) {
          this.populate(value);
        } else {
          this.reset();
        }
      },
    },

    'form.departamento_id'() {
      if (!this.form.departamento_id) {
        this.form.ciudad_id = null;
        return;
      }

      if (!this.form.ciudad_id) {
        return;
      }

      const ciudad = this.ciudades.find((item) => Number(item.id) === Number(this.form.ciudad_id));
      const coincide = ciudad && Number(ciudad.departamento_id) === Number(this.form.departamento_id);

      if (!coincide) {
        this.form.ciudad_id = null;
      }
    },

    'form.ciudad_id'() {
      if (!this.form.ciudad_id || !this.form.departamento_id) {
        return;
      }

      const ciudad = this.ciudades.find((item) => Number(item.id) === Number(this.form.ciudad_id));
      const coincide = ciudad && Number(ciudad.departamento_id) === Number(this.form.departamento_id);

      if (!coincide) {
        this.form.ciudad_id = null;
      }
    },
  },

  mounted() {
    this.cargarCatalogosUbicacion();
  },

  beforeDestroy() {
    this.clearValidationTimer();
  },

  methods: {
    extraerListaCatalogo(payload, posiblesLlaves = []) {
      if (Array.isArray(payload)) {
        if (payload.length === 1 && Array.isArray(payload[0])) {
          return payload[0];
        }

        return payload;
      }

      if (payload && typeof payload === 'object') {
        for (const llave of posiblesLlaves) {
          if (Array.isArray(payload[llave])) {
            return payload[llave];
          }
        }

        const primeraLista = Object.values(payload).find((value) => Array.isArray(value));
        return Array.isArray(primeraLista) ? primeraLista : [];
      }

      return [];
    },

    async cargarCatalogosUbicacion() {
      try {
        const [departamentosRes, ciudadesRes] = await Promise.all([
          api.get('/departamentos'),
          api.get('/ciudades'),
        ]);

        this.departamentos = this.extraerListaCatalogo(departamentosRes?.data, ['departamentos']);
        this.ciudades = this.extraerListaCatalogo(ciudadesRes?.data, ['ciudades']);

        if (this.form.id) {
          this.form.departamento_id = this.resolverDepartamentoId(this.form.departamento_id, this.form.departamento);
          this.form.ciudad_id = this.resolverCiudadId(this.form.ciudad_id, this.form.ciudad, this.form.departamento_id);
        }
      } catch (error) {
        this.departamentos = [];
        this.ciudades = [];
      }
    },

    resolverDepartamentoId(actualId, nombreDepartamento) {
      const numericId = Number(actualId || 0);
      if (numericId > 0) {
        return numericId;
      }

      const nombre = String(nombreDepartamento || '').trim().toLowerCase();
      if (!nombre) {
        return null;
      }

      const match = this.departamentos.find((item) => String(item.nombre || '').trim().toLowerCase() === nombre);
      return match ? Number(match.id) : null;
    },

    resolverCiudadId(actualId, nombreCiudad, departamentoId) {
      const numericId = Number(actualId || 0);
      if (numericId > 0) {
        return numericId;
      }

      const nombre = String(nombreCiudad || '').trim().toLowerCase();
      if (!nombre) {
        return null;
      }

      const match = this.ciudades.find(
        (item) =>
          String(item.nombre || '').trim().toLowerCase() === nombre &&
          (!departamentoId || Number(item.departamento_id) === Number(departamentoId))
      );

      return match ? Number(match.id) : null;
    },

    clearValidationTimer() {
      if (this.validationTimer) {
        window.clearTimeout(this.validationTimer);
        this.validationTimer = null;
      }
    },

    showValidationMessage(text) {
      this.clearValidationTimer();
      this.validationMessage = text;
      this.validationTimer = window.setTimeout(() => {
        this.validationMessage = '';
        this.validationTimer = null;
      }, 3000);
    },

    validateForm() {
      const requiredFields = [
        { value: this.form.nombre_comercial, label: 'Nombre comercial' },
        { value: this.form.razon_social, label: 'Razón social' },
        { value: this.form.nit, label: 'NIT' },
        { value: this.form.email, label: 'Correo de la empresa' },
        { value: this.form.telefono, label: 'Teléfono' },
        { value: this.form.direccion, label: 'Dirección' },
        { value: this.form.ciudad_id, label: 'Ciudad' },
        { value: this.form.departamento_id, label: 'Departamento' },
      ];

      for (const field of requiredFields) {
        if (!field.value) {
          this.showValidationMessage(`Por favor completa el campo ${field.label}.`);
          return false;
        }
      }

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(this.form.email)) {
        this.showValidationMessage('Por favor ingresa un correo de empresa válido.');
        return false;
      }

      if (!this.isEditMode) {
        const adminFields = [
          { value: this.form.admin_nombre, label: 'Nombres del administrador' },
          { value: this.form.admin_apellido, label: 'Apellidos del administrador' },
          { value: this.form.admin_email_user, label: 'Usuario del administrador' },
          { value: this.form.admin_telefono, label: 'Teléfono del administrador' },
          { value: this.form.admin_password, label: 'Contraseña inicial' },
        ];

        for (const field of adminFields) {
          if (!field.value) {
            this.showValidationMessage(`Por favor completa el campo ${field.label}.`);
            return false;
          }
        }

        const adminEmail = this.buildAdminEmail();

        if (!emailPattern.test(adminEmail)) {
          this.showValidationMessage('Por favor ingresa un usuario de administrador válido.');
          return false;
        }

        const pwd = this.form.admin_password;
        if (pwd.length < 8) {
          this.showValidationMessage('La contraseña debe tener al menos 8 caracteres.');
          return false;
        }
        if (!/[0-9]/.test(pwd)) {
          this.showValidationMessage('La contraseña debe contener al menos un número.');
          return false;
        }
        if (!/[A-Z]/.test(pwd)) {
          this.showValidationMessage('La contraseña debe contener al menos una letra mayúscula.');
          return false;
        }
        if (!/[a-z]/.test(pwd)) {
          this.showValidationMessage('La contraseña debe contener al menos una letra minúscula.');
          return false;
        }
      }

      this.clearValidationTimer();
      this.validationMessage = '';
      return true;
    },

    attemptSubmit() {
      if (!this.isEditMode && !this.form.admin_email_user) {
        this.form.admin_email_user = this.slugify([
          this.form.admin_nombre,
          this.form.nombre_comercial,
          this.form.nit,
        ].find(Boolean) || 'usuario');
      }

      if (!this.validateForm()) {
        return;
      }

      this.form.admin_email = this.buildAdminEmail();
      this.$emit('start-action', this.isEditMode ? 'Actualizando empresa...' : 'Creando empresa...', null, null);
      this.$emit('submit', { ...this.form });
    },

    populate(empresa) {
      this.form = {
        id: empresa.id,
        nombre_comercial: empresa.nombre_comercial || '',
        razon_social: empresa.razon_social || '',
        nit: empresa.nit || '',
        email: empresa.email || '',
        telefono: empresa.telefono || '',
        direccion: empresa.direccion || '',
        ciudad_id: empresa.ciudad_id ? Number(empresa.ciudad_id) : null,
        departamento_id: empresa.departamento_id ? Number(empresa.departamento_id) : null,
        ciudad: empresa.ciudad || '',
        departamento: empresa.departamento || '',
        logo: empresa.logo || '',
        estado_id: empresa.estado_id || null,
        admin_nombre: '',
        admin_apellido: '',
        admin_email_user: '',
        admin_email: '',
        admin_telefono: '',
        admin_password: '',
      };

      if (this.departamentos.length || this.ciudades.length) {
        this.form.departamento_id = this.resolverDepartamentoId(this.form.departamento_id, this.form.departamento);
        this.form.ciudad_id = this.resolverCiudadId(this.form.ciudad_id, this.form.ciudad, this.form.departamento_id);
      }
    },

    reset() {
      this.form = {
        id: null,
        nombre_comercial: '',
        razon_social: '',
        nit: '',
        email: '',
        telefono: '',
        direccion: '',
        ciudad_id: null,
        departamento_id: null,
        ciudad: '',
        departamento: '',
        logo: '',
        estado_id: null,
        admin_nombre: '',
        admin_apellido: '',
        admin_email_user: '',
        admin_email: '',
        admin_telefono: '',
        admin_password: '',
      };
    },

    buildAdminEmail() {
      const rawUser = String(this.form.admin_email_user || '').trim().toLowerCase();

      if (rawUser) {
        if (rawUser.includes('@')) {
          return rawUser;
        }

        return `${rawUser}@logistikpro.com`;
      }

      const base = this.slugify([
        this.form.admin_nombre,
        this.form.admin_apellido,
        this.form.nombre_comercial,
        this.form.nit,
      ].find(Boolean) || 'usuario');

      return `${base || 'usuario'}@logistikpro.com`;
    },

    slugify(value) {
      return String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '.')
        .replace(/(^\.|\.$)/g, '')
        .replace(/\.{2,}/g, '.');
    },
  },
};
</script>

<style scoped>
.form-card {
  padding: 24px;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(23, 48, 79, 0.08);
  box-shadow: 0 20px 48px rgba(14, 28, 54, 0.08);
}

.form-header {
  display: flex;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 22px;
}

.form-kicker {
  display: block;
  margin-bottom: 6px;
  color: rgba(23, 48, 79, 0.58);
  text-transform: uppercase;
  letter-spacing: 0.14em;
  font-size: 0.72rem;
}

h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #17304f;
}

.company-form {
  display: grid;
  gap: 16px;
}

.grid {
  display: grid;
  gap: 14px;
}

.grid-1 {
  grid-template-columns: 1fr;
}

.grid-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.grid-3 {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.field span {
  display: block;
  margin-bottom: 8px;
  color: rgba(23, 48, 79, 0.8);
  font-size: 0.9rem;
  font-weight: 700;
}

.field input,
.field select {
  width: 100%;
  height: 50px;
  padding: 0 14px;
  border-radius: 14px;
  border: 1px solid rgba(23, 48, 79, 0.14);
  background: rgba(248, 250, 253, 0.96);
  color: #17304f;
  outline: none;
}

.field input:focus,
.field select:focus {
  border-color: rgba(250, 175, 1, 0.8);
  box-shadow: 0 0 0 4px rgba(250, 175, 1, 0.12);
}

.password-wrap {
  display: flex;
  align-items: stretch;
  overflow: hidden;
  border-radius: 14px;
  border: 1px solid rgba(23, 48, 79, 0.14);
  background: rgba(248, 250, 253, 0.96);
}

.password-wrap input {
  border: 0;
  border-radius: 0;
  flex: 1 1 auto;
  min-width: 0;
  background: transparent;
  padding-right: 8px;
}

.ghost-button {
  border: 0;
  width: 44px;
  margin: 3px;
  border-radius: 11px;
  background: rgba(23, 48, 79, 0.08);
  color: #17304f;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: none;
}

.ghost-button i {
  font-size: 1.08rem;
}

.ghost-button:hover {
  background: rgba(23, 48, 79, 0.08);
}

.admin-email-row {
  display: flex;
  align-items: stretch;
  overflow: hidden;
  border-radius: 14px;
  border: 1px solid rgba(23, 48, 79, 0.14);
  background: rgba(248, 250, 253, 0.96);
  min-height: 50px;
}

.admin-email-row input {
  border: 0;
  border-radius: 0;
  flex: 1 1 auto;
  min-width: 0;
  background: transparent;
  height: 48px;
}

.email-domain {
  display: inline-flex;
  align-items: center;
  padding: 0 14px;
  border-left: 1px solid rgba(23, 48, 79, 0.1);
  color: rgba(23, 48, 79, 0.7);
  background: rgba(241, 245, 249, 0.95);
  font-weight: 800;
  white-space: nowrap;
  letter-spacing: 0.04em;
}

.field-note {
  display: block;
  margin-top: 8px;
  color: rgba(23, 48, 79, 0.58);
  font-size: 0.78rem;
}

.separator {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 4px;
  color: #17304f;
  font-weight: 800;
}

.separator::before,
.separator::after {
  content: '';
  height: 1px;
  flex: 1;
  background: rgba(23, 48, 79, 0.12);
}

.actions {
  display: flex;
  justify-content: flex-end;
}

.submit-button {
  height: 50px;
  padding: 0 18px;
  border: 0;
  border-radius: 14px;
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
  font-weight: 800;
  cursor: pointer;
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: wait;
}

@media (max-width: 900px) {
  .grid-2,
  .grid-3 {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .form-card {
    padding: 16px;
    border-radius: 20px;
  }

  .form-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}
</style>
