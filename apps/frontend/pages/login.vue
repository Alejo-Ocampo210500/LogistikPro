<template>
  <v-app>
    <v-main class="login-surface">
      <v-container fluid class="fill-height pa-0">
        <v-row no-gutters class="fill-height">
          <v-col cols="12" md="6" lg="5" class="brand-panel pa-7 pa-md-8 d-flex flex-column justify-start order-1 order-md-1">
            <div class="brand-title-wrap d-flex align-center mb-3">
              <div class="text-h3 text-md-h2 font-weight-black white--text brand-title mb-0">
                LogistikPro
              </div>

              <v-avatar tile size="88" class="brand-title-logo ml-3">
                <v-img :src="logo" alt="Logo LogistikPro" contain />
              </v-avatar>
            </div>

            <div class="text-h6 font-weight-regular white--text text--lighten-1 mb-4">
              Software empresarial para operar, controlar y escalar con precisión.
            </div>

            <div class="feature-stack mt-2">
              <v-card v-for="(feature, index) in features" :key="feature.title" rounded="lg" elevation="0"
                class="pa-4 feature-card mb-2" :style="{ '--feature-delay': `${index * 90}ms` }">
                <div class="d-flex align-start">
                  <v-avatar size="38" class="mr-3 feature-icon-wrap">
                    <v-icon small color="#f4b640">{{ feature.icon }}</v-icon>
                  </v-avatar>

                  <div>
                    <div class="subtitle-1 font-weight-bold white--text mb-1">
                      {{ feature.title }}
                    </div>
                    <div class="body-2 white--text text--lighten-2">
                      {{ feature.description }}
                    </div>
                  </div>
                </div>
              </v-card>
            </div>

            <div class="brand-lower-zone mt-4">
              <div class="ops-ribbon">
                <div class="d-flex align-center justify-space-between flex-wrap">
                  <div class="d-flex align-center mr-2 mb-1 mb-sm-0">
                    <span class="ops-live-dot mr-2"></span>
                    <span class="caption font-weight-bold text-uppercase ops-kicker">Operación en línea</span>
                  </div>

                  <span class="caption ops-meta">Monitoreo unificado de ventas, inventario y entregas</span>
                </div>
              </div>
            </div>

            <div class="brand-signature d-flex align-center mt-auto pt-4">
              <v-icon small color="#f4b640" class="mr-2">mdi-domain</v-icon>
              <span class="subtitle-2 font-weight-medium white--text text--lighten-2">
                Desarrollado por SOFTNOVA SOLUTIONS
              </span>
            </div>
          </v-col>

          <v-col cols="12" md="6" lg="7" class="form-panel d-flex align-center justify-center pa-4 pa-sm-6 pa-md-10 order-2 order-md-2">
            <v-responsive max-width="560" width="100%" class="form-wrap">
              <div class="form-brand-showcase mb-5">
                <div class="form-brand-orb">
                  <v-img :src="logo" alt="Logo LogistikPro" contain class="form-brand-image" />
                </div>
              </div>

              <LoginForm @authenticated="forwardSession" @start-action="forwardStartAction"
                @stop-action="forwardStopAction" />
            </v-responsive>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import LoginForm from '@/components/auth/LoginForm.vue';

export default {
  name: 'LoginView',
  layout: 'LoginLayout',

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
      logo: '/branding/logoPrincipal.png',

      features: [
        {
          icon: 'mdi-cash-register',
          title: 'Ventas y Facturación',
          description: 'Registra y controla transacciones con flujo de caja en tiempo real.',
        },
        {
          icon: 'mdi-warehouse',
          title: 'Inventario Inteligente',
          description: 'Monitorea existencias, entradas y salidas de productos sin fricción.',
        },
        {
          icon: 'mdi-chart-line',
          title: 'Reportes Estratégicos',
          description: 'Analiza indicadores críticos para tomar decisiones de alto impacto.',
        },
        {
          icon: 'mdi-account-group-outline',
          title: 'Clientes y Equipos',
          description: 'Centraliza clientes, usuarios y permisos con flujos de trabajo ordenados.',
        },
        {
          icon: 'mdi-truck-delivery-outline',
          title: 'Operación y Entregas',
          description: 'Supervisa pedidos y trazabilidad logística desde un mismo entorno.',
        },
      ],

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
.login-surface {
  background: linear-gradient(105deg, #1a3d73 0%, #214f92 51%, #e7eef9 51%, #edf4ff 100%);
}

.brand-panel {
  background: linear-gradient(180deg, #1a3768 0%, #193566 55%, #172f5a 100%);
}

.form-panel {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.26) 0%, rgba(255, 255, 255, 0.52) 100%);
  position: relative;
  overflow: hidden;
}

.form-panel::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: repeating-linear-gradient(132deg,
      rgba(244, 182, 64, 0) 0,
      rgba(244, 182, 64, 0) 20px,
      rgba(244, 182, 64, 0.15) 20px,
      rgba(244, 182, 64, 0.15) 22px);
  opacity: 0.35;
  pointer-events: none;
}

.form-panel::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 80% 15%, rgba(244, 182, 64, 0.18) 0%, rgba(244, 182, 64, 0) 44%);
  pointer-events: none;
}

.form-wrap {
  position: relative;
  z-index: 1;
}

.feature-stack {
  width: 86%;
  max-width: 620px;
}

.brand-isotipo {
  box-shadow: 0 10px 26px rgba(7, 20, 45, 0.24);
}

.brand-title {
  letter-spacing: 0.01em;
  text-shadow: 0 8px 20px rgba(3, 10, 28, 0.35);
}

.brand-title-wrap {
  flex-wrap: wrap;
}

.brand-title-logo {
  box-shadow: 0 10px 26px rgba(7, 20, 45, 0.24);
}

.form-brand-showcase {
  display: flex;
  justify-content: center;
  position: relative;
}

.form-brand-showcase::before {
  content: '';
  position: absolute;
  width: 210px;
  height: 210px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(244, 182, 64, 0.28) 0%, rgba(244, 182, 64, 0.04) 52%, rgba(244, 182, 64, 0) 72%);
  filter: blur(6px);
  transform: translateY(-8px);
  pointer-events: none;
}

.form-brand-orb {
  width: 168px;
  height: 168px;
  border-radius: 50%;
  padding: 18px;
  position: relative;
  background:
    radial-gradient(circle at 50% 40%, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 68%),
    linear-gradient(150deg, rgba(34, 67, 119, 0.34) 0%, rgba(26, 58, 112, 0.14) 48%, rgba(244, 182, 64, 0.16) 100%);
  border: 1px solid rgba(103, 132, 184, 0.34);
  box-shadow:
    0 16px 42px rgba(25, 54, 102, 0.28),
    inset 0 0 0 1px rgba(255, 255, 255, 0.34);
}

.form-brand-image {
  width: 100%;
  height: 100%;
  filter: drop-shadow(0 10px 12px rgba(12, 30, 64, 0.32));
}

.feature-card {
  position: relative;
  overflow: hidden;
  isolation: isolate;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0.13) 100%);
  border: 1px solid rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(3px);
  box-shadow: 0 10px 22px rgba(7, 20, 45, 0.16);
  min-height: 92px;
  transform: translate3d(0, 0, 0);
  transition: transform 0.36s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.36s ease, border-color 0.36s ease;
  animation: featureReveal 0.62s cubic-bezier(0.16, 1, 0.3, 1) both;
  animation-delay: var(--feature-delay, 0ms);
}

.feature-card::before {
  content: '';
  position: absolute;
  left: 14px;
  right: 14px;
  top: 0;
  height: 2px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(244, 182, 64, 0.4) 0%, rgba(244, 182, 64, 0.9) 48%, rgba(244, 182, 64, 0.35) 100%);
  opacity: 0.72;
}

.feature-card::after {
  content: '';
  position: absolute;
  top: -140%;
  left: -24%;
  width: 46%;
  height: 320%;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.26) 48%, rgba(255, 255, 255, 0) 100%);
  transform: rotate(18deg);
  opacity: 0;
  transition: left 0.52s ease, opacity 0.32s ease;
}

.feature-icon-wrap {
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.24);
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
  transition: transform 0.36s ease, box-shadow 0.36s ease, background 0.36s ease;
}

.brand-lower-zone {
  padding: 2px 0;
}

.ops-ribbon {
  border-radius: 12px;
  padding: 10px 12px;
  border: 1px solid rgba(255, 255, 255, 0.14);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.06) 100%);
}

.ops-live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #f4b640;
  box-shadow: 0 0 0 5px rgba(244, 182, 64, 0.16);
}

.ops-kicker {
  color: rgba(227, 236, 251, 0.9);
  letter-spacing: 0.08em;
}

.ops-meta {
  color: rgba(216, 229, 248, 0.82);
}

.feature-card .subtitle-1,
.feature-card .body-2 {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

@media (hover: hover) and (pointer: fine) {
  .feature-card:hover {
    transform: translateY(-5px);
    border-color: rgba(244, 182, 64, 0.36);
    box-shadow: 0 16px 30px rgba(7, 20, 45, 0.26);
  }

  .feature-card:hover::after {
    left: 108%;
    opacity: 1;
  }

  .feature-card:hover .feature-icon-wrap {
    transform: translateY(-2px) scale(1.06);
    background: rgba(255, 255, 255, 0.26);
    box-shadow: 0 7px 16px rgba(4, 17, 40, 0.18);
  }

  .feature-card:hover .subtitle-1 {
    transform: translateX(1px);
  }
}

@keyframes featureReveal {
  from {
    opacity: 0;
    transform: translateY(16px) scale(0.985);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.brand-signature {
  border-top: 1px solid rgba(255, 255, 255, 0.16);
}

@media (max-width: 959px) {
  .login-surface {
    background: linear-gradient(180deg, #1b3d74 0%, #214f93 58%, #eef4ff 58%, #eef4ff 100%);
  }

  .brand-panel {
    padding-top: 24px !important;
    padding-bottom: 26px !important;
  }

  .form-panel {
    min-height: 100svh;
    padding-top: 24px !important;
    padding-bottom: 18px !important;
  }

  .form-wrap {
    max-width: 620px;
  }

  .brand-isotipo {
    width: 82px !important;
    height: 82px !important;
  }

  .brand-title-logo {
    width: 68px !important;
    height: 68px !important;
    margin-left: 10px !important;
  }

  .brand-title {
    font-size: 2rem !important;
    line-height: 1.1 !important;
    margin-bottom: 0 !important;
  }

  .form-brand-showcase::before {
    width: 184px;
    height: 184px;
  }

  .form-brand-orb {
    width: 146px;
    height: 146px;
    padding: 16px;
  }

  .feature-stack {
    width: 100%;
    max-width: 100%;
  }

  .brand-lower-zone {
    margin-top: 10px !important;
  }

  .feature-card {
    min-height: auto;
    padding: 12px !important;
    animation-duration: 0.46s;
  }

  .brand-signature {
    margin-top: 8px !important;
    padding-top: 12px !important;
  }
}

@media (max-width: 600px) {
  .login-surface {
    background: linear-gradient(180deg, #1b3d74 0%, #214f93 52%, #eef4ff 52%, #eef4ff 100%);
  }

  .brand-panel {
    padding-left: 18px !important;
    padding-right: 18px !important;
  }

  .form-panel {
    padding-left: 14px !important;
    padding-right: 14px !important;
  }

  .feature-card .subtitle-1 {
    font-size: 0.95rem !important;
    line-height: 1.2 !important;
  }

  .feature-card .body-2 {
    font-size: 0.8rem !important;
    line-height: 1.25rem !important;
  }

  .ops-ribbon {
    padding: 10px;
  }

  .ops-meta {
    width: 100%;
  }

  .brand-title-wrap {
    align-items: flex-end !important;
  }

  .brand-title-logo {
    width: 56px !important;
    height: 56px !important;
    margin-left: 8px !important;
  }

  .form-brand-showcase {
    margin-bottom: 14px !important;
  }

  .form-brand-showcase::before {
    width: 156px;
    height: 156px;
    transform: translateY(-4px);
  }

  .form-brand-orb {
    width: 124px;
    height: 124px;
    padding: 13px;
  }
}
</style>
