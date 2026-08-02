<template>
  <main class="auth-page">
    <div class="glow glow-one"></div>
    <div class="glow glow-two"></div>
    <div class="glow glow-three"></div>

    <section class="auth-shell">
      <aside class="hero-panel">
        <div class="hero-brand">
          <div class="hero-mark">
            <img :src="logo" alt="LogistikPro" class="hero-logo" />
          </div>
          <div>
            <span class="hero-kicker">VERSIÓN 1.0</span>
            <h1>LogistikPro</h1>
          </div>
        </div>

        <p class="hero-copy">
          La plataforma integral para gestionar ventas, inventario y operaciones empresariales con seguridad,
          trazabilidad y control en tiempo real.
        </p>

        <div class="hero-features">
          <article>
            <span>01</span>
            <strong>Ventas y Facturación</strong>
            <p>Procesa ventas de manera rápida y mantén el control de caja y pagos en tiempo real.</p>
          </article>

          <article>
            <span>02</span>
            <strong>Inventario Inteligente</strong>
            <p>Gestiona productos mediante códigos de barras, existencias y movimientos de inventario.</p>
          </article>

          <article>
            <span>03</span>
            <strong>Reportes Estratégicos</strong>
            <p>Visualiza indicadores clave de ventas, utilidades y desempeño empresarial.</p>
          </article>
        </div>

        <div class="hero-note">
          <span class="note-label">LogistikPro - VERSIÓN 1.0</span>
          <strong>Transforma datos en decisiones.</strong>
          <small>Información en tiempo real para administrar y hacer crecer tu empresa.</small>
        </div>
      </aside>

      <section class="form-panel">
        <LoginForm @authenticated="forwardSession" @start-action="forwardStartAction" @stop-action="forwardStopAction" />
      </section>
    </section>
  </main>
</template>

<script>
import logo from '@/assets/branding/isotipo-logistikpro.png';
import LoginForm from '@/components/auth/LoginForm.vue';

export default {
  name: 'LoginView',

  components: {
    LoginForm,
  },

  props: {
    session: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      logo,
    };
  },

  methods: {
    forwardSession(payload) {
      this.$emit('authenticated', payload);
    },

    forwardStartAction(message) {
      this.$emit('start-action', message);
    },

    forwardStopAction() {
      this.$emit('stop-action');
    },
  },
};
</script>

<style scoped>
.hero-kicker {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: fit-content;

  padding: 6px 14px;
  background: #faaf01;
  color: white;
  border-radius: 50px;

  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;

  box-shadow: 0 6px 15px rgba(250, 175, 1, 0.3);
}

.auth-page {
  position: relative;
  min-height: 100vh;
  overflow: hidden;
  padding: 32px;
}

.auth-shell {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: minmax(0, 1.1fr) minmax(420px, 0.9fr);
  gap: 36px;
  align-items: center;
  max-width: 1360px;
  min-height: calc(100vh - 64px);
  margin: 0 auto;
}

.hero-panel {
  display: grid;
  gap: 30px;
  padding: 36px 12px 36px 0;
}

.hero-brand {
  display: flex;
  align-items: center;
  gap: 18px;
}

.hero-mark {
  display: grid;
  place-items: center;
  width: 176px;
  height: 176px;
  flex: 0 0 auto;
  border-radius: 34px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(3, 10, 28, 0.94));
  border: 1px solid rgba(255, 255, 255, 0.14);
  box-shadow: 0 26px 70px rgba(0, 0, 0, 0.32);
}

.hero-logo {
  width: 156px;
  height: 156px;
  object-fit: contain;
  border-radius: 24px;
}

.hero-kicker {
  display: block;
  margin-bottom: 8px;
  color: rgba(245, 247, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 0.18em;
  font-size: 0.72rem;
}

h1 {
  margin: 0;
  font-size: clamp(3rem, 6vw, 5.8rem);
  line-height: 0.94;
  letter-spacing: -0.06em;
  color: #ffffff !important;
}

.hero-copy {
  max-width: 680px;
  margin: 0;
  color: rgba(255, 255, 255, 0.9) !important;
  font-size: 1.08rem;
  line-height: 1.8;
}

.hero-features {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

.hero-features article {
  padding: 20px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.09);
  backdrop-filter: blur(16px);
}

.hero-features span {
  display: inline-block;
  margin-bottom: 16px;
  color: #ffd67a !important;
  font-size: 0.84rem;
  font-weight: 800;
  letter-spacing: 0.12em;
}

.hero-features strong {
  display: block;
  margin-bottom: 8px;
  font-size: 1rem;
  color: #ffffff !important;
}

.hero-features p {
  margin: 0;
  color: rgba(255, 255, 255, 0.75) !important;
  line-height: 1.6;
}

.hero-note {
  width: min(100%, 440px);
  padding: 24px 26px;
  border-radius: 24px;
  background: linear-gradient(135deg, rgba(244, 183, 64, 0.12), rgba(255, 255, 255, 0.05));
  border: 1px solid rgba(244, 183, 64, 0.22);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.hero-note strong {
  display: block;
  margin: 8px 0 4px;
  font-size: 1.08rem;
  color: #ffffff !important;
}

.hero-note small {
  color: rgba(255, 255, 255, 0.8) !important;
}

.note-label {
  display: block;
  color: rgba(255, 255, 255, 0.6) !important;
  letter-spacing: 0.16em;
  font-size: 0.72rem;
}

.form-panel {
  display: grid;
  justify-items: center;
  align-items: center;
}

.glow {
  position: absolute;
  border-radius: 999px;
  filter: blur(20px);
  opacity: 0.8;
  pointer-events: none;
}

.glow-one {
  width: 540px;
  height: 540px;
  top: -150px;
  left: -150px;
  background: radial-gradient(circle, rgba(60, 121, 255, 0.26), transparent 66%);
}

.glow-two {
  width: 780px;
  height: 780px;
  right: -180px;
  top: 120px;
  background: radial-gradient(circle, rgba(244, 183, 64, 0.12), transparent 64%);
}

.glow-three {
  width: 620px;
  height: 620px;
  bottom: -220px;
  left: 32%;
  background: radial-gradient(circle, rgba(31, 83, 195, 0.18), transparent 62%);
}

@media (max-width: 1100px) {
  .auth-shell {
    grid-template-columns: 1fr;
    padding-top: 8px;
    padding-bottom: 24px;
  }

  .hero-panel {
    padding-right: 0;
  }

  .hero-features {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .auth-page {
    padding: 16px;
  }

  .hero-brand {
    align-items: flex-start;
  }

  .hero-logo {
    width: 132px;
    height: 132px;
  }

  .hero-mark {
    width: 150px;
    height: 150px;
  }
}
</style>
