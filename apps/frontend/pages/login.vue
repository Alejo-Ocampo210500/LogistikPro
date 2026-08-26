<template>
  <v-app>
    <v-main class="login-surface">
      <div class="industrial-grid"></div>
      <div class="signal-line signal-line--one"></div>
      <div class="signal-line signal-line--two"></div>
      <v-container fluid class="login-shell pa-0">
        <v-row no-gutters class="login-layout">
          <v-col cols="12" md="7" class="brand-panel order-2 order-md-1">
            <div class="brand-content">
              <div class="brand-header d-flex align-center">
                <v-img :src="logo" alt="Logo LogistikPro" contain class="brand-mark mr-3" />
                <div>
                  <div class="brand-name">Logistik<span>Pro</span></div>
                  <div class="brand-code">PLATAFORMA OPERATIVA</div>
                </div>
              </div>

              <div class="brand-copy">
                <div class="eyebrow mb-4"><span class="eyebrow-dot"></span>Operación empresarial conectada</div>
                <h1>Control total para<br><span>mover tu negocio.</span></h1>
                <p>Software empresarial para operar, controlar y escalar con precisión.</p>
              </div>

              <div class="feature-grid">
                <div v-for="(feature, index) in features" :key="feature.title" class="feature-item">
                  <div class="feature-number">0{{ index + 1 }}</div>
                  <v-icon color="#f4b640" size="22">{{ feature.icon }}</v-icon>
                  <div class="feature-text">
                    <strong>{{ feature.title }}</strong>
                    <span>{{ feature.description }}</span>
                  </div>
                </div>
              </div>

              <div class="brand-footer d-flex align-center justify-space-between flex-wrap">
                <div class="system-status d-flex align-center"><span class="status-pulse"></span>En línea</div>
                <span>Desarrollado por SOFTNOVA SOLUTIONS</span>
              </div>
            </div>
          </v-col>

          <v-col cols="12" md="5" class="form-panel order-1 order-md-2 d-flex align-center justify-center">
            <div class="form-column">
              <div class="mobile-brand d-flex d-md-none align-center mb-8">
                <v-img :src="logo" alt="Logo LogistikPro" contain class="mobile-mark" />
                <div>
                  <div class="mobile-name">Logistik<span>Pro</span></div>
                  <div class="mobile-caption">Centro de operaciones</div>
                </div>
              </div>
              <div class="access-meta d-none d-md-flex align-center mb-6">
                <span>ACCESO / OPERADORES</span>
              </div>
              <LoginForm @authenticated="forwardSession" @start-action="forwardStartAction" @stop-action="forwardStopAction" />
              <div class="secure-note d-flex align-center justify-center mt-6">
                <v-icon size="15" color="#8292aa" class="mr-2">mdi-shield-check-outline</v-icon>
                Conexión cifrada y acceso protegido
              </div>
            </div>
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
  components: { LoginForm },
  props: { session: { type: Object, default: null } },
  data() {
    return {
      logo: '/branding/logoPrincipal.png',
      features: [
        { icon: 'mdi-cash-register', title: 'Ventas y Facturación', description: 'Flujo de caja en tiempo real.' },
        { icon: 'mdi-warehouse', title: 'Inventario Inteligente', description: 'Existencias siempre bajo control.' },
        { icon: 'mdi-chart-line', title: 'Reportes Estratégicos', description: 'Indicadores para decidir mejor.' },
        { icon: 'mdi-truck-delivery-outline', title: 'Operación y Entregas', description: 'Trazabilidad de inicio a fin.' },
      ],
    };
  },
  methods: {
    async forwardSession(payload) {
      this.$emit('authenticated', payload);
      await this.$router.push('/inicio/principal');
    },
    forwardStartAction(message) { this.$emit('start-action', message); },
    forwardStopAction() { this.$emit('stop-action'); },
  },
};
</script>

<style scoped>
.login-surface { position: relative; min-height: 100vh; overflow: hidden; background: #06142b; color: #fff; }
.industrial-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(106, 133, 175, 0.07) 1px, transparent 1px), linear-gradient(90deg, rgba(106, 133, 175, 0.07) 1px, transparent 1px); background-size: 42px 42px; mask-image: linear-gradient(90deg, #000 0%, rgba(0, 0, 0, 0.7) 58%, transparent 100%); pointer-events: none; }
.signal-line { position: absolute; width: 280px; height: 1px; background: linear-gradient(90deg, transparent, rgba(244, 182, 64, 0.58), transparent); transform: rotate(-45deg); pointer-events: none; }
.signal-line--one { left: -70px; top: 24%; }
.signal-line--two { left: 47%; bottom: 7%; }
.login-shell, .login-layout { min-height: 100vh; }
.brand-panel { position: relative; background: radial-gradient(circle at 72% 30%, rgba(20, 59, 122, 0.56), transparent 42%), linear-gradient(145deg, rgba(8, 28, 59, 0.58), rgba(4, 15, 34, 0.16)); border-right: 1px solid rgba(131, 156, 197, 0.15); }
.brand-content { position: relative; z-index: 1; min-height: 100vh; max-width: 880px; padding: clamp(32px, 5vw, 72px); display: flex; flex-direction: column; }
.brand-mark { flex: 0 0 auto; width: 84px; height: 84px; mix-blend-mode: screen; filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.28)); }
.brand-name, .mobile-name { font-size: 28px; font-weight: 900; letter-spacing: -0.04em; }
.brand-name span, .mobile-name span { color: #f4b640; }
.brand-code, .mobile-caption { color: #8292aa; font-size: 10px; font-weight: 700; letter-spacing: 0.16em; }
.brand-copy { margin: auto 0 40px; }
.eyebrow { color: #aebbd0; font-size: 11px; font-weight: 800; letter-spacing: 0.16em; text-transform: uppercase; }
.eyebrow-dot { display: inline-block; width: 8px; height: 8px; margin-right: 10px; background: #f4b640; box-shadow: 0 0 0 5px rgba(244, 182, 64, 0.12); }
.brand-copy h1 { margin: 0; max-width: 760px; font-size: clamp(44px, 5.4vw, 76px); line-height: 0.98; letter-spacing: -0.055em; font-weight: 900; }
.brand-copy h1 span { color: #f4b640; }
.brand-copy p { max-width: 560px; margin: 26px 0 0; color: #aebbd0; font-size: 18px; line-height: 1.65; }
.feature-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
.feature-item { position: relative; min-height: 106px; padding: 22px; display: flex; align-items: flex-start; gap: 14px; background: rgba(6, 20, 43, 0.72); border: 1px solid rgba(130, 153, 190, 0.14); border-radius: 18px; transition: background 0.25s ease, transform 0.25s ease; }
.feature-item:hover { background: rgba(15, 43, 83, 0.9); }
.feature-number { position: absolute; top: 10px; right: 12px; color: rgba(143, 163, 195, 0.28); font-size: 10px; font-weight: 800; letter-spacing: 0.12em; }
.feature-text { display: flex; flex-direction: column; }
.feature-text strong { margin-bottom: 5px; color: #f5f7fb; font-size: 14px; }
.feature-text span { color: #8292aa; font-size: 12px; line-height: 1.45; }
.brand-footer { margin-top: 30px; color: #65758f; font-size: 9px; font-weight: 700; letter-spacing: 0.13em; }
.system-status { color: #a5b2c7; text-transform: uppercase; }
.status-pulse { width: 7px; height: 7px; margin-right: 9px; border-radius: 50%; background: #49c18f; box-shadow: 0 0 0 4px rgba(73, 193, 143, 0.12); }
.form-panel { position: relative; z-index: 2; padding: 40px clamp(28px, 5vw, 74px); background: #eef2f7; box-shadow: -30px 0 80px rgba(0, 0, 0, 0.18); }
.form-panel::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 28%; background: #f4b640; }
.form-column { width: 100%; max-width: 470px; }
.access-meta { color: #65758f; font-size: 10px; font-weight: 800; letter-spacing: 0.14em; }
.secure-note { color: #8292aa; font-size: 11px; letter-spacing: 0.03em; }
.mobile-mark { flex: 0 0 auto; width: 128px; height: 128px; mix-blend-mode: screen; filter: drop-shadow(0 14px 28px rgba(0, 0, 0, 0.3)); }
.mobile-name, .mobile-caption { display: none; }
.mobile-brand { justify-content: center; padding: 0; }
@media (max-width: 959px) {
  .login-surface { overflow: auto; background: #06142b; }
  .login-layout { min-height: auto; }
  .form-panel { min-height: 100svh; padding: 48px 28px; border-radius: 0; background: transparent; box-shadow: none; }
  .form-panel::before { display: none; }
  .brand-panel { display: none; }
  .brand-content { min-height: auto; padding: 52px 24px 36px; }
  .brand-header { display: none !important; }
  .brand-copy { margin: 0 0 34px; }
  .brand-copy h1 { font-size: clamp(38px, 10vw, 58px); }
  .brand-copy p { font-size: 16px; }
}
@media (max-width: 600px) {
  .form-panel { min-height: 100svh; padding: 38px 0 44px; align-items: center !important; }
  .form-column { width: calc(100% - 32px); max-width: 460px; }
  .mobile-brand { margin-bottom: 20px !important; }
  .feature-grid { grid-template-columns: 1fr; }
  .brand-footer { gap: 16px; line-height: 1.5; }
}
</style>
