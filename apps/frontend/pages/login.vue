<template>
  <v-app>
    <v-main class="login-surface">
      <v-container fluid class="fill-height pa-0">
        <v-row no-gutters class="fill-height">
          <v-col cols="12" md="6" lg="5" class="brand-panel pa-7 pa-md-8 d-flex flex-column justify-center order-1 order-md-1">
            <div class="d-flex align-center justify-space-between mb-5">
              <v-avatar tile size="102" class="brand-isotipo">
                <v-img :src="logo" alt="Logo LogistikPro" contain />
              </v-avatar>

              <v-chip small label color="#f4b640" text-color="#1a2b4a" class="font-weight-bold">
                VERSIÓN 1.0
              </v-chip>
            </div>

            <div class="text-h3 text-md-h2 font-weight-black white--text mb-3 brand-title">
              LogistikPro
            </div>

            <div class="text-h6 font-weight-regular white--text text--lighten-1 mb-4">
              Software empresarial para operar, controlar y escalar con precisión.
            </div>

            <div class="feature-stack mt-2">
              <v-card v-for="feature in features" :key="feature.title" rounded="lg" elevation="0"
                class="pa-4 feature-card mb-2">
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

            <div class="brand-signature d-flex align-center mt-auto pt-4">
              <v-icon small color="#f4b640" class="mr-2">mdi-domain</v-icon>
              <span class="subtitle-2 font-weight-medium white--text text--lighten-2">
                Desarrollado por SOFTNOVA SOLUTIONS
              </span>
            </div>
          </v-col>

          <v-col cols="12" md="6" lg="7" class="form-panel d-flex align-center justify-center pa-4 pa-sm-6 pa-md-10 order-2 order-md-2">
            <v-responsive max-width="560" width="100%" class="form-wrap">
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

.feature-card {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0.13) 100%);
  border: 1px solid rgba(255, 255, 255, 0.18);
  box-shadow: 0 10px 22px rgba(7, 20, 45, 0.16);
  min-height: 92px;
}

.feature-icon-wrap {
  background: rgba(255, 255, 255, 0.18);
  border: 1px solid rgba(255, 255, 255, 0.24);
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

  .brand-title {
    font-size: 2rem !important;
    line-height: 1.1 !important;
    margin-bottom: 10px !important;
  }

  .feature-stack {
    width: 100%;
    max-width: 100%;
  }

  .feature-card {
    min-height: auto;
    padding: 12px !important;
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
}
</style>
