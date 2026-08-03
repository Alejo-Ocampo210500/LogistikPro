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

      <div class="grid grid-3">
        <label class="field">
          <span>NIT</span>
          <input v-model.trim="form.nit" type="text" placeholder="900000123" />
        </label>

        <label class="field">
          <span>Plan</span>
          <select v-model.number="form.plan_id">
            <option v-for="option in planes" :key="option.id || option" :value="option.id">
              {{ formatPlan(option) }}
            </option>
          </select>
        </label>

        <label class="field">
          <span>Teléfono</span>
          <input v-model.trim="form.telefono" type="text" placeholder="3000000000" />
        </label>
      </div>

      <div class="grid grid-2">
        <label class="field">
          <span>Correo de la empresa</span>
          <input v-model.trim="form.email" type="email" placeholder="contacto@empresa.com" />
        </label>

        <label class="field">
          <span>Fecha de vencimiento</span>
          <input v-model="form.fecha_vencimiento" type="date" />
        </label>
      </div>

      <div class="grid grid-2">
        <label class="field">
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
          <span>Ciudad</span>
          <input v-model.trim="form.ciudad" type="text" placeholder="Medellín" />
        </label>

        <label class="field">
          <span>Departamento</span>
          <input v-model.trim="form.departamento" type="text" placeholder="Antioquia" />
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
          <input v-model.trim="form.admin_telefono" type="text" placeholder="3000000000" />
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
        ciudad: '',
        departamento: '',
        logo: '',
        plan_id: null,
        estado_id: null,
        fecha_vencimiento: '',
        admin_nombre: '',
        admin_apellido: '',
        admin_email_user: '',
        admin_email: '',
        admin_telefono: '',
        admin_password: '',
      },
      showAdminPassword: false,
      validationMessage: '',
      validationTimer: null,
    };
  },

  computed: {
    isEditMode() {
      return this.mode === 'editar';
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
  },

  methods: {
    formatPlan(value) {
      if (!value) {
        return '-';
      }

      if (typeof value === 'object' && value.nombre) {
        value = value.nombre;
      }

      return String(value).charAt(0).toUpperCase() + String(value).slice(1);
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
          { value: this.form.ciudad, label: 'Ciudad' },
          { value: this.form.departamento, label: 'Departamento' },
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

        if (!this.form.plan_id) {
          this.showValidationMessage('Selecciona un plan para la empresa.');
          return false;
        }

        if (!this.form.fecha_vencimiento) {
          this.showValidationMessage('Por favor selecciona una fecha de vencimiento.');
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

    beforeDestroy() {
      this.clearValidationTimer();
    },

    populate(empresa) {
      const selectedPlan = this.planes.find(plan => plan.id === empresa.plan_id || plan.nombre === empresa.plan);

      this.form = {
        id: empresa.id,
        nombre_comercial: empresa.nombre_comercial || '',
        razon_social: empresa.razon_social || '',
        nit: empresa.nit || '',
        email: empresa.email || '',
        telefono: empresa.telefono || '',
        direccion: empresa.direccion || '',
        ciudad: empresa.ciudad || '',
        departamento: empresa.departamento || '',
        logo: empresa.logo || '',
        plan_id: selectedPlan ? selectedPlan.id : empresa.plan_id || null,
        estado_id: empresa.estado_id || null,
        fecha_vencimiento: empresa.fecha_vencimiento ? String(empresa.fecha_vencimiento).slice(0, 10) : '',
        admin_nombre: '',
        admin_apellido: '',
        admin_email_user: '',
        admin_email: '',
        admin_telefono: '',
        admin_password: '',
      };
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
        ciudad: '',
        departamento: '',
        logo: '',
        plan_id: this.planes.length ? this.planes[0].id : null,
        estado_id: this.estados.length ? this.estados[0].id : null,
        fecha_vencimiento: '',
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
