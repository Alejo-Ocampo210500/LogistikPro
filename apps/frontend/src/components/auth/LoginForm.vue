<template>
  <section class="login-card">
    <div class="card-top">
      <div class="card-brand">
        <div class="card-mark">
          <img :src="logo" alt="LogistikPro" class="card-logo" />
        </div>
        <div>
          <span class="eyebrow">Acceso corporativo</span>
          <strong>LogistikPro</strong>
        </div>
      </div>
    </div>

    <h2>Iniciar sesión</h2>
    <p class="subtitle">
      Ingresa con tus credenciales para acceder al entorno de tu empresa.
    </p>

    <form class="login-form" @submit.prevent="submitLogin">
      <label class="field">
        <span>Correo electrónico</span>
        <input
          v-model.trim="form.email"
          type="email"
          autocomplete="email"
          placeholder="usuario@empresa.com"
          :disabled="loading"
        />
      </label>

      <label class="field">
        <span>Contraseña</span>
        <div class="password-wrap">
          <input
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="current-password"
            placeholder="Ingresa tu contraseña"
            :disabled="loading"
          />
          <button
            type="button"
            class="ghost-button"
            @click="showPassword = !showPassword"
            :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
          >
            <i :class="showPassword ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline'"></i>
          </button>
        </div>
      </label>

      <div class="helpers">
        <label class="remember">
          <input v-model="rememberMe" type="checkbox" />
          <span>Recordar este equipo</span>
        </label>
      </div>

      <button class="submit-button" type="submit" :disabled="loading">
        <span v-if="loading" class="button-inline">
          <span class="button-spinner" aria-hidden="true"></span>
          Iniciando sesión en LogistikPro...
        </span>
        <span v-else>Entrar al sistema</span>
      </button>
    </form>

    <transition name="fade-slide">
      <div v-if="message" :class="['auth-alert', alertType]" role="alert">
        {{ message }}
      </div>
    </transition>
  </section>
</template>

<script>
import api from '@/services/api';
import logo from '@/assets/branding/isotipo-logistikpro.png';

export default {
  name: 'LoginForm',

  data() {
    return {
      logo,
      loading: false,
      rememberMe: true,
      showPassword: false,
      message: '',
      alertType: 'error',
      messageTimer: null,
      form: {
        email: '',
        password: '',
      },
    };
  },

  beforeDestroy() {
    if (this.messageTimer) {
      window.clearTimeout(this.messageTimer);
    }
  },

  methods: {
    clearMessageTimer() {
      if (this.messageTimer) {
        window.clearTimeout(this.messageTimer);
        this.messageTimer = null;
      }
    },

    showMessage(type, text) {
      this.clearMessageTimer();
      this.alertType = type;
      this.message = text;
      this.messageTimer = window.setTimeout(() => {
        this.message = '';
        this.messageTimer = null;
      }, 3000);
    },

    storeSession(data) {
      const storage = this.rememberMe ? localStorage : sessionStorage;

      storage.setItem('logistikpro_token', data.token);
      storage.setItem('logistikpro_empresa_id', String(data.empresa_id || data.user?.empresa_id || ''));
      storage.setItem('logistikpro_user', JSON.stringify(data.user));
      storage.setItem('logistikpro_empresa', JSON.stringify(data.empresa));
      storage.setItem('logistikpro_rol', JSON.stringify(data.rol));
    },

    resolveError(error) {
      if (error.response && error.response.status === 422 && error.response.data && error.response.data.errors) {
        const errors = Object.values(error.response.data.errors).reduce((carry, current) => carry.concat(current), []);
        return errors[0] || 'Revisa los campos del formulario.';
      }

      if (error.response && error.response.data && error.response.data.mensaje) {
        return error.response.data.mensaje;
      }

      return 'No fue posible iniciar sesión.';
    },

    validateCredentials() {
      if (!this.form.email || !this.form.password) {
        this.showMessage('error', 'Por favor ingresa tu correo y contraseña.');
        return false;
      }

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailPattern.test(this.form.email)) {
        this.showMessage('error', 'Ingresa un correo electrónico válido.');
        return false;
      }

      return true;
    },

    async submitLogin() {
      if (!this.validateCredentials()) {
        return;
      }

      this.$emit('start-action', 'Iniciando sesión en LogistikPro...', null, null);
      this.loading = true;
      this.message = '';

      try {
        const { data } = await api.post('/login', this.form);
        this.storeSession(data);
        this.$emit('authenticated', data);
      } catch (error) {
        this.showMessage('error', this.resolveError(error));
      } finally {
        this.loading = false;
        this.$emit('stop-action');
      }
    },
  },
};
</script>

<style scoped>
.login-card {
  width: 100%;
  max-width: 520px;
  padding: 34px;
  border-radius: 30px;
  background: linear-gradient(180deg, rgba(10, 22, 52, 0.94), rgba(5, 14, 33, 0.98));
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 32px 90px rgba(0, 0, 0, 0.38);
  backdrop-filter: blur(22px);
}

.card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.card-brand {
  display: flex;
  align-items: center;
  gap: 14px;
}

.card-mark {
  display: grid;
  place-items: center;
  width: 88px;
  height: 88px;
  flex: 0 0 auto;
  border-radius: 24px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(3, 10, 28, 0.94));
  border: 1px solid rgba(255, 255, 255, 0.14);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.26);
}

.card-logo {
  width: 72px;
  height: 72px;
  object-fit: contain;
  border-radius: 18px;
}

.eyebrow {
  display: block;
  margin-bottom: 4px;
  color: rgba(255, 255, 255, 0.6) !important;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
}

.card-brand strong {
  display: block;
  font-size: 1.06rem;
  color: #ffffff !important;
}

h2 {
  margin: 0;
  font-size: 2.05rem;
  line-height: 1.05;
  color: #ffffff !important;
}

.subtitle {
  margin: 10px 0 24px;
  color: rgba(255, 255, 255, 0.8) !important;
  line-height: 1.7;
}

.login-form {
  display: grid;
  gap: 16px;
}

.field span {
  display: block;
  margin-bottom: 8px;
  color: rgba(255, 255, 255, 0.9) !important;
  font-size: 0.93rem;
  font-weight: 600;
}

.field input {
  width: 100%;
  height: 54px;
  padding: 0 16px;
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  background: rgba(255, 255, 255, 0.04);
  color: #ffffff !important;
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.field input::placeholder {
  color: rgba(255, 255, 255, 0.4) !important;
}

.field input:focus {
  border-color: rgba(244, 183, 64, 0.76);
  box-shadow: 0 0 0 4px rgba(244, 183, 64, 0.11);
}

.password-wrap {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}

.password-wrap input {
  width: 100%;
  padding-right: 64px;
}

.ghost-button {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%) !important;
  border: 0;
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.08);
  color: rgba(255, 255, 255, 0.8) !important;
  cursor: pointer;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  box-shadow: none !important;
  filter: none !important;
  transition: none !important;
  animation: none !important;
  z-index: 2;
}

.ghost-button i {
  font-size: 1.1rem;
}

.ghost-button::before,
.ghost-button::after {
  content: none !important;
  display: none !important;
  animation: none !important;
}

.ghost-button:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.08);
  color: rgba(255, 255, 255, 0.8) !important;
  transform: translateY(-50%) !important;
  box-shadow: none !important;
  filter: none !important;
}

.ghost-button:active:not(:disabled) {
  transform: translateY(-50%) !important;
  box-shadow: none !important;
  filter: none !important;
}

.login-card .ghost-button,
.login-card .ghost-button:hover:not(:disabled),
.login-card .ghost-button:active:not(:disabled) {
  transform: translateY(-50%) !important;
}

.helpers {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.remember {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: rgba(255, 255, 255, 0.8) !important;
  font-size: 0.92rem;
}

.remember input {
  accent-color: var(--lp-gold);
}

.submit-button {
  height: 56px;
  border: 0;
  border-radius: 16px;
  background: linear-gradient(135deg, #f4b740 0%, #ffd277 45%, #d99210 100%);
  color: #0b1530;
  font-weight: 800;
  letter-spacing: 0.02em;
  cursor: pointer;
  box-shadow: 0 18px 30px rgba(244, 183, 64, 0.28);
  transition: box-shadow 0.18s ease, opacity 0.18s ease;
  position: relative;
  overflow: hidden;
}

.submit-button:hover:not(:disabled) {
  transform: none;
  box-shadow: 0 22px 34px rgba(244, 183, 64, 0.34);
}

.submit-button:active:not(:disabled) {
  transform: none;
}

.submit-button:disabled {
  opacity: 0.72;
  cursor: wait;
}

.login-card button:not(.ghost-button):hover:not(:disabled),
.login-card button:not(.ghost-button):active:not(:disabled) {
  transform: none !important;
}

.button-inline {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.button-spinner {
  width: 16px;
  height: 16px;
  border-radius: 999px;
  border: 2px solid rgba(11, 21, 48, 0.24);
  border-top-color: #0b1530;
  animation: spin 0.8s linear infinite;
}


.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.22s ease;
}

.fade-slide-enter,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 640px) {
  .login-card {
    padding: 24px;
    border-radius: 24px;
  }

  h2 {
    font-size: 1.72rem;
  }

  .helpers {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }
}
</style>
