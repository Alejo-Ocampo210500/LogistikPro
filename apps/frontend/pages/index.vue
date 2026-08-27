<template>
  <div class="landing-page">
    <section id="inicio" class="hero-section">
      <v-container class="py-6 py-sm-8 py-md-14">
        <v-row align="center" class="hero-grid">
          <v-col cols="12" md="6" class="pr-md-8">
            <div class="text-overline section-kicker mb-2">
              PLATAFORMA DE GESTIÓN EMPRESARIAL
            </div>
            <h1 class="hero-title mb-4">
              Controla tu operación, optimiza tus recursos y haz crecer tu negocio.
            </h1>
            <p class="hero-copy mb-6">
              LogistikPro centraliza ventas, inventario, compras, clientes y reportes en una sola plataforma,
              brindándote la información que necesitas para tomar mejores decisiones.
            </p>

            <div class="d-flex flex-column flex-sm-row hero-cta-wrap">
              <v-btn x-large rounded class="mb-3 mb-sm-0 mr-sm-3 brand-gold-btn"
                href="https://wa.me/573122891144?text=Hola%2C%20quiero%20solicitar%20una%20demo%20de%20LogistikPro."
                target="_blank">
                SOLICITAR DEMO
              </v-btn>
              <v-btn x-large rounded outlined class="brand-gold-btn-outline" href="#plataforma">
                Explorar plataforma
              </v-btn>
            </div>

            <v-row class="mt-6" dense>
              <v-col v-for="stat in quickStats" :key="stat.label" cols="6" sm="4">
                <v-sheet rounded="xl" class="pa-4 metric-chip" elevation="4">
                  <div class="metric-value">
                    {{ stat.value }}
                  </div>
                  <div class="metric-label">
                    {{ stat.label }}
                  </div>
                </v-sheet>
              </v-col>
            </v-row>
          </v-col>

          <v-col cols="12" md="6" class="mt-2 mt-md-0">
            <div class="hero-visual-wrap">
              <v-sheet rounded="xl" outlined elevation="12" class="hero-banner-shell">
                <v-img src="/branding/bannerNuevo.png" class="hero-banner-image" height="520"
                  position="center center" />
              </v-sheet>

              <v-sheet rounded="xl" class="floating-card floating-card--bottom pa-3" elevation="10">
                <v-icon color="accent" class="mr-2" small>
                  mdi-chart-line
                </v-icon>
                Control y analítica empresarial
              </v-sheet>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <section id="plataforma" class="section-block section-block--alt">
      <v-container class="py-8 py-md-14 white--text">
        <v-row align="end" class="mb-6">
          <v-col cols="12" md="10" lg="8">
            <div class="text-overline section-kicker mb-2">
              Plataforma LogistikPro
            </div>
            <h2 class="text-h4 font-weight-bold mb-3">
              Gestión empresarial inteligente, integrada y centralizada
            </h2>
            <div class="text-body-1 section-copy">
              Diseñado para empresas que necesitan control total: ventas, inventario, compras, finanzas,
              facturación electrónica, cumplimiento DIAN, seguridad y analítica en una sola plataforma.
            </div>
          </v-col>
        </v-row>

        <v-row>
          <v-col v-for="item in modules" :key="item.title" cols="12" sm="6" lg="3">
            <v-card rounded="xl" outlined elevation="4"
              class="pa-4 pa-sm-5 fill-height lp-card module-panel-card white--text">
              <div class="d-flex align-center justify-space-between mb-3">
                <v-icon :color="item.color" size="30">
                  {{ item.icon }}
                </v-icon>
                <v-chip x-small outlined class="module-status-chip">
                  {{ item.scope }}
                </v-chip>
              </div>

              <div class="text-h6 font-weight-bold mb-2">
                {{ item.title }}
              </div>
              <div class="text-body-2 mb-3 section-copy">
                {{ item.text }}
              </div>

              <v-list dense color="transparent" class="pa-0">
                <v-list-item v-for="line in item.lines" :key="line" class="px-0 min-h-0">
                  <v-list-item-icon class="mr-3">
                    <v-icon small color="accent">
                      mdi-minus
                    </v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title class="text-body-2 white--text">
                      {{ line }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <v-container id="planes" class="py-10 py-md-14 white--text">
      <v-row align="end" class="mb-6">
        <v-col cols="12" md="8">
          <div class="text-overline section-kicker mb-2">
            Planes
          </div>
          <h2 class="text-h4 font-weight-bold mb-3">
            Escala tu operación con LogistikPro
          </h2>
          <div class="text-body-1 section-copy">
            Comienza con lo esencial y evoluciona cuando lo necesites. Elige un plan según el tamaño de tu equipo y
            lleva la gestión de tu empresa al siguiente nivel.
          </div>
        </v-col>
      </v-row>

      <v-row>
        <v-col v-for="plan in plans" :key="plan.title" cols="12" sm="6" lg="3">
          <v-card rounded="xl" outlined elevation="4" class="pa-5 pa-sm-6 fill-height plan-card lp-card white--text"
            :class="{ 'plan-card--featured': plan.featured }">
            <v-chip v-if="plan.badge" label small class="mb-4 plan-badge-chip">
              {{ plan.badge }}
            </v-chip>

            <div class="text-h5 font-weight-bold mb-2">
              {{ plan.title }}
            </div>

            <div class="text-body-2 mb-4 d-inline-flex align-center plan-users-pill">
              <v-icon small class="mr-2" color="accent">
                mdi-account-group-outline
              </v-icon>
              {{ plan.users }}
            </div>

            <v-divider class="my-4 plan-divider" />

            <div class="plan-pricing-grid">
              <div class="plan-price-box">
                <div class="plan-price-label">
                  Mensual
                </div>
                <div class="plan-price-value">
                  {{ plan.monthly }}
                </div>
              </div>

              <div class="plan-price-box">
                <div class="plan-price-label">
                  Anual
                </div>
                <div class="plan-price-value">
                  {{ plan.yearly }}
                </div>
              </div>
            </div>

            <div class="text-body-2 mt-4 section-copy">
              {{ plan.text }}
            </div>

            <div class="text-caption text-uppercase mt-4 mb-2 plan-modules-title">
              Módulos habilitados
            </div>

            <v-list dense color="transparent" class="pa-0">
              <v-list-item v-for="module in plan.modules" :key="module" class="px-0 min-h-0">
                <v-list-item-icon class="mr-3">
                  <v-icon small color="accent">
                    mdi-check
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title class="text-body-2 white--text">
                    {{ module }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>

            <v-btn block x-large rounded class="mt-6 brand-gold-btn" href="#contacto">
              Seleccionar plan
            </v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-container id="contacto" class="py-10 py-md-14">
      <v-card rounded="xl" outlined elevation="10" class="pa-5 pa-sm-6 pa-md-10 contact-card white--text">
        <v-row>
          <v-col cols="12" md="5" class="pr-md-8 mb-8 mb-md-0">
            <div class="text-overline section-kicker mb-2">
              Contacto Profesional
            </div>
            <h2 class="text-h4 font-weight-bold mb-3 contact-title">
              Conversemos sobre el crecimiento de tu empresa
            </h2>
            <div class="text-body-1 section-copy mb-6">
              Cuéntanos tu necesidad y te ayudamos a definir el mejor plan para tu operación. Nuestro equipo responde
              en corto tiempo.
            </div>

            <v-list dense color="transparent" class="pa-0 contact-info-list">
              <v-list-item class="px-0 mb-2">
                <v-list-item-icon class="mr-3">
                  <v-icon color="accent">
                    mdi-email-outline
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title class="text-body-1 white--text font-weight-bold">
                    Correo corporativo
                  </v-list-item-title>
                  <v-list-item-subtitle class="contact-info-value">
                    <a href="mailto:gmailcontactologistikpro@gmail.com" class="contact-link">
                      gmailcontactologistikpro@gmail.com
                    </a>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <v-list-item class="px-0 mb-2">
                <v-list-item-icon class="mr-3">
                  <v-icon color="accent">
                    mdi-whatsapp
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title class="text-body-1 white--text font-weight-bold">
                    WhatsApp comercial
                  </v-list-item-title>
                  <v-list-item-subtitle class="contact-info-value">
                    <a href="https://wa.me/573122891144" target="_blank" rel="noopener" class="contact-link">
                      312 2891144
                    </a>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <v-list-item class="px-0">
                <v-list-item-icon class="mr-3">
                  <v-icon color="accent">
                    mdi-clock-time-four-outline
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title class="text-body-1 white--text font-weight-bold">
                    Horario de atención
                  </v-list-item-title>
                  <v-list-item-subtitle class="contact-info-value">
                    Lunes a viernes, 7:00 a.m. - 5:00 p.m. | Sábados, 7:00 a.m. - 2:00 p.m.
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-col>

          <v-col cols="12" md="7">
            <v-card rounded="xl" outlined class="pa-4 pa-sm-5 contact-form-shell">
              <div class="text-subtitle-1 text-sm-h6 font-weight-bold mb-4 white--text">
                Formulario de sugerencias y contacto
              </div>
              <v-form ref="contactFormRef" v-model="isContactFormValid" lazy-validation
                @submit.prevent="submitContactForm">
                <v-row dense>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="contactForm.name" dense outlined hide-details="auto" class="contact-input"
                      label="Nombre completo" :rules="contactRules.name" />
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="contactForm.company" dense outlined hide-details="auto" class="contact-input"
                      label="Empresa" />
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="contactForm.email" dense outlined hide-details="auto" class="contact-input"
                      label="Correo electrónico" :rules="contactRules.email" />
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="contactForm.phone" dense outlined hide-details="auto" class="contact-input"
                      label="Teléfono" :rules="contactRules.phone" />
                  </v-col>
                  <v-col cols="12">
                    <v-select v-model="contactForm.topic" dense outlined hide-details="auto" class="contact-input"
                      :items="contactTopics" label="Tema de interés" :rules="contactRules.topic" />
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="contactForm.message" outlined rows="4" no-resize hide-details="auto"
                      class="contact-input" label="Mensaje o sugerencia" :rules="contactRules.message" counter="1200" />
                  </v-col>
                  <v-col cols="12">
                    <v-checkbox v-model="contactForm.acceptPolicy" hide-details color="accent"
                      class="contact-check white--text"
                      label="Acepto el tratamiento de datos para recibir respuesta comercial"
                      :rules="contactRules.acceptPolicy" />
                  </v-col>
                  <v-col cols="12" v-if="contactFeedback.message">
                    <v-alert dense text outlined :type="contactFeedback.type" class="contact-feedback">
                      {{ contactFeedback.message }}
                    </v-alert>
                  </v-col>
                </v-row>

                <v-progress-linear v-if="isSubmittingContact" indeterminate color="accent" rounded class="mt-1" />

                <div class="d-flex flex-column flex-sm-row align-sm-center justify-space-between mt-4 contact-actions">
                  <div class="text-caption section-copy mb-3 mb-sm-0 d-flex align-center">
                    <v-icon small color="accent" class="mr-2">
                      mdi-lightning-bolt-outline
                    </v-icon>
                    Respuesta estimada: menos de 24 horas hábiles.
                  </div>
                  <v-btn x-large rounded type="submit" class="brand-gold-btn contact-submit-btn"
                    :loading="isSubmittingContact">
                    Enviar solicitud
                  </v-btn>
                </div>
              </v-form>
            </v-card>
          </v-col>
        </v-row>
      </v-card>
    </v-container>

    <section class="site-footer-credit">
      <v-container class="py-4 py-sm-5">
        <div class="d-flex align-center justify-center text-center footer-credit-wrap">
          <v-icon small color="accent" class="mr-2">
            mdi-code-tags
          </v-icon>
          <span>SOFTNOVA SOLUTIONS 2026</span>
        </div>
      </v-container>
    </section>
  </div>
</template>

<script>
export default {
  name: 'IndexPage',
  data() {
    return {
      quickStats: [
        { value: '360°', label: 'Visibilidad de tu negocio' },
        { value: '24/7', label: 'Visibilidad del negocio' },
        { value: 'ESCALABLE', label: 'Crece junto a tu negocio' }
      ],
      modules: [
        {
          title: 'Comercial y CRM',
          text: 'Gestiona prospectos, clientes, cotizaciones y pedidos en un flujo comercial integrado.',
          icon: 'mdi-cog-outline',
          color: 'cyan lighten-1',
          scope: 'Core',
          lines: ['Pipeline y seguimiento comercial', 'Cotizaciones y pedidos', 'Historial y trazabilidad del cliente']
        },
        {
          title: 'Inventario y Bodega',
          text: 'Control de existencias, movimientos, costos y disponibilidad por sede o almacén.',
          icon: 'mdi-warehouse',
          color: 'amber darken-2',
          scope: 'Core',
          lines: ['Kardex y movimientos', 'Mínimos, máximos y alertas', 'Trazabilidad por lote o referencia']
        },
        {
          title: 'Compras y Proveedores',
          text: 'Automatiza abastecimiento desde requisición hasta recepción y control documental.',
          icon: 'mdi-cart-check',
          color: 'green lighten-1',
          scope: 'Core',
          lines: ['Órdenes de compra', 'Recepción y validación', 'Evaluación de proveedores']
        },
        {
          title: 'Tesorería y Cartera',
          text: 'Administra cuentas por cobrar y pagar, recaudos, pagos y flujo de caja.',
          icon: 'mdi-cash-multiple',
          color: 'indigo lighten-1',
          scope: 'Finanzas',
          lines: ['Estado de cartera', 'Programación de pagos', 'Conciliaciones y movimientos']
        },
        {
          title: 'Contabilidad y Costos',
          text: 'Consolida información financiera para cierres contables, análisis y cumplimiento.',
          icon: 'mdi-calculator-variant-outline',
          color: 'deep-purple lighten-1',
          scope: 'Finanzas',
          lines: ['Asientos automáticos', 'Centros de costo', 'Balance y estados financieros']
        },
        {
          title: 'Facturación Electrónica',
          text: 'Emisión, validación y gestión de documentos electrónicos según normativa vigente.',
          icon: 'mdi-file-document-check-outline',
          color: 'orange darken-2',
          scope: 'DIAN',
          lines: ['Facturas y notas electrónicas', 'Eventos y acuses', 'Gestión de contingencias']
        },
        {
          title: 'Cumplimiento DIAN',
          text: 'Estructura fiscal y tributaria preparada para reportes y obligaciones regulatorias.',
          icon: 'mdi-bank-check',
          color: 'teal lighten-1',
          scope: 'DIAN',
          lines: ['Parametrización tributaria', 'Soporte para reportes exigidos', 'Control de vencimientos y estados']
        },
        {
          title: 'Seguridad y Analítica',
          text: 'Gobierno de accesos y visibilidad ejecutiva con indicadores para toma de decisiones.',
          icon: 'mdi-shield-account-outline',
          color: 'pink lighten-1',
          scope: 'Gestión',
          lines: ['Roles y permisos por perfil', 'Auditoría de acciones', 'Dashboards gerenciales']
        }
      ],
      plans: [
        {
          title: 'Starter',
          users: 'Hasta 3 usuarios',
          monthly: '$79.900/mes',
          yearly: '$862.920/año',
          text: 'Ideal para equipos pequeños que quieren iniciar con control operativo en una sola plataforma.',
          modules: [
            'Comercial y CRM',
            'Inventario y Bodega',
            'Compras y Proveedores'
          ],
          badge: 'Base',
          featured: false,
        },
        {
          title: 'Business',
          users: 'Hasta 10 usuarios',
          monthly: '$149.900/mes',
          yearly: '$1.618.920/año',
          text: 'Enfoque recomendado para compañías en crecimiento que requieren mayor capacidad de usuarios.',
          modules: [
            'Comercial y CRM',
            'Inventario y Bodega',
            'Compras y Proveedores',
            'Tesorería y Cartera',
            'Contabilidad y Costos'
          ],
          badge: 'Recomendado',
          featured: true,
        },
        {
          title: 'Professional',
          users: 'Hasta 25 usuarios',
          monthly: '$279.900/mes',
          yearly: '$3.022.920/año',
          text: 'Pensado para operaciones más robustas que requieren colaboración de múltiples áreas.',
          modules: [
            'Comercial y CRM',
            'Inventario y Bodega',
            'Compras y Proveedores',
            'Tesorería y Cartera',
            'Contabilidad y Costos',
            'Facturación Electrónica',
            'Cumplimiento DIAN'
          ],
          badge: 'Avanzado',
          featured: false,
        },
        {
          title: 'Enterprise',
          users: '26+ usuarios',
          monthly: 'Personalizado',
          yearly: 'Personalizado',
          text: 'Plan a la medida para organizaciones con requerimientos especiales de escala y gobierno.',
          modules: [
            'Todos los módulos Professional',
            'Seguridad y Analítica avanzada',
            'Parametrización a medida',
            'Acompañamiento especializado'
          ],
          badge: 'Corporativo',
          featured: false,
        }
      ],
      contactTopics: [
        'Solicitar demo comercial',
        'Asesoría de implementación',
        'Sugerencia funcional',
        'Integraciones y desarrollo',
        'Soporte general'
      ],
      contactForm: {
        name: '',
        company: '',
        email: '',
        phone: '',
        topic: '',
        message: '',
        acceptPolicy: false
      },
      isContactFormValid: false,
      isSubmittingContact: false,
      contactFeedback: {
        type: 'success',
        message: ''
      },
      contactRules: {
        name: [
          value => !!value || 'Ingresa tu nombre completo.',
          value => (value && value.length <= 120) || 'El nombre no debe superar 120 caracteres.'
        ],
        email: [
          value => !!value || 'Ingresa tu correo electrónico.',
          value => /.+@.+\..+/.test(value) || 'Ingresa un correo válido.'
        ],
        phone: [
          value => !!value || 'Ingresa un número de contacto.',
          value => (value && value.length <= 30) || 'El teléfono no debe superar 30 caracteres.'
        ],
        topic: [
          value => !!value || 'Selecciona un tema de interés.'
        ],
        message: [
          value => !!value || 'Escribe tu mensaje o sugerencia.',
          value => (value && value.length <= 1200) || 'El mensaje no debe superar 1200 caracteres.'
        ],
        acceptPolicy: [
          value => !!value || 'Debes aceptar el tratamiento de datos.'
        ]
      },
      zones: [
        {
          title: 'Sitio público',
          text: 'La cara visible de LogistikPro para informar, captar interés y ordenar la narrativa comercial.',
          icon: 'mdi-earth',
          color: 'info',
          tags: ['Marca', 'Producto', 'Conversión']
        },
        {
          title: 'Superadmin',
          text: 'Zona reservada para administración central, gobierno y configuración de la plataforma.',
          icon: 'mdi-crown-outline',
          color: 'warning',
          tags: ['Control', 'Permisos', 'Gobierno']
        },
        {
          title: 'Clientes',
          text: 'Espacio operativo para usuarios finales, separado de la capa pública y de administración.',
          icon: 'mdi-account-group-outline',
          color: 'success',
          tags: ['Operación', 'Acceso', 'Uso diario']
        }
      ]
    }
  },
  methods: {
    async submitContactForm() {
      this.contactFeedback.message = ''

      const isValid = this.$refs.contactFormRef?.validate()

      if (!isValid) {
        this.contactFeedback = {
          type: 'warning',
          message: 'Revisa los campos marcados para poder enviar tu solicitud.'
        }
        return
      }

      this.isSubmittingContact = true

      try {
        const payload = {
          name: this.contactForm.name,
          company: this.contactForm.company,
          email: this.contactForm.email,
          phone: this.contactForm.phone,
          topic: this.contactForm.topic,
          message: this.contactForm.message,
          acceptPolicy: this.contactForm.acceptPolicy
        }

        const response = await this.$api.$post('/contact/suggestions', payload)

        this.contactFeedback = {
          type: 'success',
          message: response?.message || 'Solicitud enviada correctamente. Te responderemos pronto.'
        }

        this.contactForm = {
          name: '',
          company: '',
          email: '',
          phone: '',
          topic: '',
          message: '',
          acceptPolicy: false
        }

        this.$refs.contactFormRef?.resetValidation()
      } catch (error) {
        const apiMessage = error?.response?.data?.message

        this.contactFeedback = {
          type: 'error',
          message: apiMessage || 'No se pudo enviar en este momento. Intenta nuevamente.'
        }
      } finally {
        this.isSubmittingContact = false
      }
    }
  }
}
</script>

<style scoped>
.landing-page {
  --lp-blue-900: #07162d;
  --lp-blue-800: #0b1f4b;
  --lp-blue-700: #12366d;
  --lp-gold-500: var(--v-accent-base, #F4B640);
  --lp-gold-300: var(--v-accent-base, #F4B640);
  --lp-text-main: #f4f7fb;
  --lp-text-soft: #b8c6e6;
  background:
    radial-gradient(circle at 8% 12%, rgba(244, 182, 64, 0.16), transparent 34%),
    radial-gradient(circle at 92% 18%, rgba(18, 54, 109, 0.42), transparent 42%),
    linear-gradient(160deg, var(--lp-blue-900) 0%, #091d3f 52%, var(--lp-blue-900) 100%);
  color: var(--lp-text-main);
  overflow-x: hidden;
}

.hero-section {
  position: relative;
}

.hero-grid {
  row-gap: 18px;
}

.hero-title {
  font-size: clamp(2rem, 5vw, 3.6rem);
  line-height: 1.05;
  letter-spacing: -0.02em;
  max-width: 16ch;
}

.hero-copy,
.section-copy,
.point-copy,
.point-subtitle,
.metric-label {
  color: var(--lp-text-soft);
}

.hero-cta-wrap .v-btn {
  min-width: 196px;
}

.hero-banner-shell {
  background: rgba(7, 22, 45, 0.66);
  backdrop-filter: blur(3px);
  border-color: rgba(255, 255, 255, 0.12) !important;
}

.hero-banner-image {
  border-radius: 24px;
  overflow: hidden;
}

.hero-visual-wrap {
  position: relative;
}

.floating-card {
  position: absolute;
  display: inline-flex;
  align-items: center;
  font-weight: 700;
  color: var(--lp-text-main);
  background: rgba(11, 31, 75, 0.92);
  border: 1px solid rgba(244, 182, 64, 0.45);
}

.floating-card--top {
  top: -18px;
  right: -10px;
}

.floating-card--bottom {
  bottom: -18px;
  left: -10px;
}

.metric-chip {
  background: rgba(11, 31, 75, 0.7);
  border: 1px solid rgba(244, 182, 64, 0.28);
  color: var(--lp-text-main);
}

.metric-value {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--lp-gold-300);
}

.section-kicker {
  color: var(--lp-gold-300);
  letter-spacing: 0.16em;
  font-weight: 700;
}

.section-block {
  position: relative;
}

.section-block--alt {
  background: linear-gradient(180deg, rgba(7, 22, 45, 0.78), rgba(7, 22, 45, 0.92));
}

.module-status-chip {
  border-color: rgba(244, 182, 64, 0.5) !important;
  color: var(--lp-gold-300) !important;
}

.lp-card,
.hero-point-card,
.contact-card {
  background: rgba(11, 31, 75, 0.78);
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
}

.contact-title {
  max-width: 16ch;
}

.contact-info-list {
  background: rgba(7, 22, 45, 0.4);
  border: 1px solid rgba(244, 182, 64, 0.26);
  border-radius: 16px;
  padding: 14px 16px !important;
}

.contact-info-value {
  color: var(--lp-text-soft) !important;
  opacity: 1;
}

.contact-link {
  color: var(--lp-gold-300);
  text-decoration: none;
  border-bottom: 1px dashed rgba(244, 182, 64, 0.45);
  transition: color 0.2s ease, border-color 0.2s ease;
}

.contact-link:hover {
  color: #ffd67e;
  border-color: rgba(255, 214, 126, 0.85);
}

.contact-form-shell {
  background: rgba(7, 22, 45, 0.68);
  border: 1px solid rgba(255, 255, 255, 0.16) !important;
  position: relative;
  overflow: hidden;
}

.contact-form-shell::before {
  content: '';
  position: absolute;
  inset: -35% auto auto -25%;
  width: 240px;
  height: 240px;
  background: radial-gradient(circle, rgba(244, 182, 64, 0.22), rgba(244, 182, 64, 0));
  pointer-events: none;
}

.contact-input :deep(.v-input__slot) {
  background: rgba(11, 31, 75, 0.72) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.contact-input :deep(.v-input__slot:hover) {
  border-color: rgba(244, 182, 64, 0.48) !important;
}

.contact-input :deep(.v-input--is-focused .v-input__slot) {
  border-color: rgba(244, 182, 64, 0.75) !important;
  box-shadow: 0 0 0 3px rgba(244, 182, 64, 0.18);
  transform: translateY(-1px);
}

.contact-input :deep(input),
.contact-input :deep(textarea),
.contact-input :deep(.v-label) {
  color: var(--lp-text-main) !important;
}

.contact-check {
  margin-top: 4px;
  padding: 10px 12px;
  border: 1px solid rgba(244, 182, 64, 0.38);
  border-radius: 12px;
  background: rgba(244, 182, 64, 0.06);
}

.contact-check :deep(.v-label) {
  color: var(--lp-text-main) !important;
}

.contact-check :deep(.v-input--selection-controls__input) {
  margin-right: 10px;
}

.contact-check :deep(.v-input--selection-controls__input .v-icon) {
  color: #f4b640 !important;
  font-size: 1.32rem !important;
}

.contact-check :deep(.v-input--selection-controls__ripple) {
  color: #f4b640 !important;
}

.contact-check :deep(.v-messages__message) {
  color: #ffd67e !important;
}

.contact-actions {
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  padding-top: 14px;
}

.contact-submit-btn {
  min-width: 220px;
  color: #13294b !important;
  font-weight: 800;
}

.contact-feedback {
  border-width: 1px;
}

.site-footer-credit {
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  background: rgba(3, 10, 22, 0.55);
}

.footer-credit-wrap {
  color: var(--lp-text-soft);
  letter-spacing: 0.06em;
  font-size: 0.78rem;
  text-transform: uppercase;
}

.hero-point-card:hover,
.module-panel-card:hover,
.plan-card:hover,
.zone-card:hover,
.contact-card:hover {
  transform: translateY(-4px);
  border-color: rgba(244, 182, 64, 0.5) !important;
  box-shadow: 0 18px 36px rgba(4, 10, 24, 0.38);
}

.hero-point-card,
.module-panel-card,
.plan-card,
.zone-card {
  height: 100%;
}

.plan-card--featured {
  border-color: rgba(244, 182, 64, 0.56) !important;
}

.plan-badge-chip {
  background: #F4B640 !important;
  color: #13294b !important;
  font-weight: 800;
}

.plan-divider {
  border-color: rgba(255, 255, 255, 0.18) !important;
}

.plan-users-pill {
  padding: 8px 12px;
  border-radius: 999px;
  background: rgba(244, 182, 64, 0.12);
  border: 1px solid rgba(244, 182, 64, 0.35);
  color: var(--lp-text-main);
  font-weight: 600;
}

.plan-pricing-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.plan-price-box {
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.16);
  background: rgba(7, 22, 45, 0.48);
}

.plan-price-label {
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--lp-text-soft);
  margin-bottom: 6px;
}

.plan-price-value {
  font-size: 1.04rem;
  font-weight: 800;
  color: var(--lp-gold-300);
  line-height: 1.2;
}

.plan-modules-title {
  color: var(--lp-gold-300);
  letter-spacing: 0.08em;
  font-weight: 700;
}

.zone-tag {
  border-color: rgba(244, 182, 64, 0.5) !important;
  color: var(--lp-gold-300) !important;
}

.brand-gold-btn {
  background: #F4B640 !important;
  color: #13294b !important;
  border: 1px solid rgba(246, 215, 146, 0.62) !important;
  box-shadow: 0 12px 28px rgba(244, 182, 64, 0.28);
  font-weight: 800;
}

.brand-gold-btn-outline {
  color: var(--lp-gold-300) !important;
  border-color: rgba(244, 182, 64, 0.65) !important;
  background: rgba(244, 182, 64, 0.08) !important;
  font-weight: 700;
}

@media (max-width: 959px) {
  .hero-title {
    max-width: 100%;
    font-size: clamp(2rem, 6.2vw, 2.9rem);
    line-height: 1.12;
  }

  .hero-copy,
  .section-copy,
  .point-copy,
  .point-subtitle {
    font-size: 0.96rem;
    line-height: 1.58;
  }

  .hero-cta-wrap {
    gap: 10px;
  }

  .hero-cta-wrap .v-btn {
    min-width: 0;
    width: 100%;
    margin-right: 0 !important;
  }

  .floating-card {
    position: static;
    margin-top: 12px;
    width: 100%;
    justify-content: center;
  }

  .hero-visual-wrap {
    margin-top: 8px;
  }

  .contact-title {
    max-width: 100%;
  }
}

@media (max-width: 599px) {
  .landing-page .container {
    padding-left: 16px;
    padding-right: 16px;
  }

  .landing-page .text-h4 {
    font-size: 1.55rem !important;
    line-height: 1.2;
  }

  .landing-page .text-h5 {
    font-size: 1.28rem !important;
  }

  .hero-banner-image {
    border-radius: 18px;
    height: 300px !important;
  }

  .hero-cta-wrap .v-btn {
    min-width: 0;
    width: 100%;
  }

  .module-status-chip {
    max-width: 84px;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
</style>
