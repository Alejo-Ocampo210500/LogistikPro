<template>
  <v-app class="app-shell">
    <transition name="boot-fade">
      <div v-if="booting" class="app-loader" aria-live="polite" aria-busy="true">
        <div class="app-loader__card">
          <div class="app-loader__mark">
            <img :src="logo" alt="LogistikPro" />
          </div>
          <div class="app-loader__text">
            <span>Preparando LogistikPro</span>
            <strong>Cargando tu espacio de trabajo...</strong>
          </div>
          <div class="app-loader__bar">
            <span></span>
          </div>
        </div>
      </div>
    </transition>

    <transition name="boot-fade">
      <div v-if="actionLoading" class="app-loader app-loader--action" aria-live="polite" aria-busy="true">
        <div class="app-loader__card">
          <div class="app-loader__mark">
            <img :src="logo" alt="LogistikPro" />
          </div>
          <div class="app-loader__text">
            <span>LogistikPro</span>
            <strong>{{ actionMessage }}</strong>
          </div>
          <div class="app-loader__bar">
            <span></span>
          </div>
        </div>
      </div>
    </transition>

    <component
      :is="activeView"
      :session="session"
      @authenticated="handleAuthenticated"
      @logout="handleLogout"
      @start-action="startActionLoader"
      @stop-action="stopActionLoader"
    />
  </v-app>
</template>

<script>
import LoginView from './views/auth/LoginView.vue';
import BlankPageView from './views/blank/BlankPageView.vue';
import SuperadminView from './views/superadmin/SuperadminView.vue';
import logo from '@/assets/branding/isotipo-logistikpro.png';

export default {
  name: 'App',

  components: {
    LoginView,
    BlankPageView,
    SuperadminView,
  },

  data() {
    return {
      logo,
      booting: true,
      bootMinDuration: 2000,
      bootStartedAt: Date.now(),
      bootTimer: null,
      actionLoading: false,
      actionMessage: 'Procesando acción...',
      actionTimer: null,
      actionCallback: null,
      buttonPreloadTimers: new WeakMap(),
      session: this.restoreSession(),
    };
  },

  mounted() {
    const elapsed = Date.now() - this.bootStartedAt;
    const remaining = Math.max(0, this.bootMinDuration - elapsed);

    this.bootTimer = window.setTimeout(() => {
      this.booting = false;
    }, remaining);

    document.addEventListener('pointerdown', this.handleGlobalButtonFeedback, true);
    document.addEventListener('keydown', this.handleKeyboardButtonFeedback, true);
    document.addEventListener('click', this.handleGlobalButtonClick, true);
  },

  beforeDestroy() {
    if (this.bootTimer) {
      window.clearTimeout(this.bootTimer);
    }

    if (this.actionTimer) {
      window.clearTimeout(this.actionTimer);
    }

    document.removeEventListener('pointerdown', this.handleGlobalButtonFeedback, true);
    document.removeEventListener('keydown', this.handleKeyboardButtonFeedback, true);
    document.removeEventListener('click', this.handleGlobalButtonClick, true);
  },

  computed: {
    activeView() {
      if (!this.session) {
        return 'LoginView';
      }

      const roleName = this.session.rol && this.session.rol.nombre ? String(this.session.rol.nombre).toLowerCase() : '';

      return (roleName === 'superadmin' || roleName === 'superadministrador') ? 'SuperadminView' : 'BlankPageView';
    },
  },

  methods: {
    startActionLoader(message, callback, duration = 2200) {
      if (this.actionTimer) {
        window.clearTimeout(this.actionTimer);
        this.actionTimer = null;
      }

      this.actionMessage = message;
      this.actionLoading = true;
      this.actionCallback = typeof callback === 'function' ? callback : null;

      if (duration !== null && duration !== undefined && duration > 0) {
        this.actionTimer = window.setTimeout(() => {
          if (this.actionCallback) {
            this.actionCallback();
            this.actionCallback = null;
          }

          this.actionLoading = false;
          this.actionTimer = null;
        }, duration);
      }
    },

    stopActionLoader() {
      if (this.actionTimer) {
        window.clearTimeout(this.actionTimer);
        this.actionTimer = null;
      }

      if (this.actionCallback) {
        this.actionCallback();
        this.actionCallback = null;
      }

      this.actionLoading = false;
    },

    handleGlobalButtonFeedback(event) {
      const button = event.target instanceof Element ? event.target.closest('button') : null;

      if (!button || button.disabled) {
        return;
      }

      button.classList.add('lp-button-preload');

      const previousTimer = this.buttonPreloadTimers.get(button);

      if (previousTimer) {
        window.clearTimeout(previousTimer);
      }

      const timer = window.setTimeout(() => {
        button.classList.remove('lp-button-preload');
      }, 780);

      this.buttonPreloadTimers.set(button, timer);
    },

    handleGlobalButtonClick(event) {
      const button = event.target instanceof Element ? event.target.closest('button') : null;

      if (!button || button.disabled || !button.hasAttribute('data-action-loader')) {
        return;
      }

      if (this.actionTimer) {
        window.clearTimeout(this.actionTimer);
      }

      this.actionMessage = 'Cargando LogistikPro...';
      this.actionLoading = true;

      this.actionTimer = window.setTimeout(() => {
        this.actionLoading = false;
      }, 2000);
    },

    handleKeyboardButtonFeedback(event) {
      if (event.key !== 'Enter' && event.key !== ' ') {
        return;
      }

      const button = event.target instanceof Element ? event.target.closest('button') : null;

      if (!button || button.disabled) {
        return;
      }

      button.classList.add('lp-button-preload');

      const previousTimer = this.buttonPreloadTimers.get(button);

      if (previousTimer) {
        window.clearTimeout(previousTimer);
      }

      const timer = window.setTimeout(() => {
        button.classList.remove('lp-button-preload');
      }, 780);

      this.buttonPreloadTimers.set(button, timer);
    },

    restoreSession() {
      const token = localStorage.getItem('logistikpro_token') || sessionStorage.getItem('logistikpro_token');

      if (!token) {
        return null;
      }

      const storage = localStorage.getItem('logistikpro_token') ? localStorage : sessionStorage;

      return {
        token,
        empresa_id: storage.getItem('logistikpro_empresa_id'),
        user: this.parseJson(storage.getItem('logistikpro_user')),
        empresa: this.parseJson(storage.getItem('logistikpro_empresa')),
        rol: this.parseJson(storage.getItem('logistikpro_rol')),
      };
    },

    parseJson(value) {
      if (!value) {
        return null;
      }

      try {
        return JSON.parse(value);
      } catch (error) {
        return null;
      }
    },

    handleAuthenticated(payload) {
      this.session = payload;
    },

    handleLogout() {
      this.startActionLoader('Cerrando sesión en LogistikPro...', () => {
        localStorage.removeItem('logistikpro_token');
        localStorage.removeItem('logistikpro_user');
        localStorage.removeItem('logistikpro_empresa');
        localStorage.removeItem('logistikpro_rol');
        localStorage.removeItem('logistikpro_empresa_id');
        sessionStorage.removeItem('logistikpro_token');
        sessionStorage.removeItem('logistikpro_user');
        sessionStorage.removeItem('logistikpro_empresa');
        sessionStorage.removeItem('logistikpro_rol');
        sessionStorage.removeItem('logistikpro_empresa_id');
        this.session = null;
      });
    },
  },
};
</script>
