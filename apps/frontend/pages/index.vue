<template>
  <div class="landing-page">
    <v-container id="inicio" class="py-8 py-md-12">
      <v-row align="center" justify="space-between" class="py-2 py-md-8">
        <v-col cols="12" md="6" class="pr-md-10 text-center text-md-left">
          <v-chip color="accent" dark label small class="mb-6">
            GESTIÓN EMPRESARIAL INTELIGENTE
          </v-chip>

          <h1 class="landing-title font-weight-bold mb-5">
            Gestiona tu negocio. Impulsa tu crecimiento.
          </h1>

          <p class="text-body-1 text-md-h6 font-weight-regular mb-8">
            LogistikPro integra ventas, inventario, clientes, facturación y administración en una sola plataforma,
            diseñada para ayudarte a controlar tu negocio de forma simple, rápida y eficiente.
          </p>

          <v-row class="mb-8" dense align="stretch">
            <v-col v-for="point in heroPoints" :key="point.title" cols="12" sm="6" lg="3">
              <v-sheet color="#0B1D3A" rounded="xl" class="pa-4 hero-point-card d-flex flex-column">
                <v-icon :color="point.color" class="mb-2" size="28">
                  {{ point.icon }}
                </v-icon>
                <div class="text-subtitle-2 font-weight-bold mb-1 hero-point-title">
                  {{ point.title }}
                </div>
                <div class="text-caption font-weight-medium mb-1 hero-point-subtitle">
                  {{ point.subtitle }}
                </div>
                <div class="text-body-2 hero-point-text">
                  {{ point.text }}
                </div>
              </v-sheet>
            </v-col>
          </v-row>

          <div class="d-flex flex-column flex-sm-row justify-center justify-md-start align-center">
            <v-btn color="accent" dark x-large rounded class="mr-sm-4 mb-4" href="#contacto">
              Solicitar demo
            </v-btn>

            <v-btn outlined x-large rounded class="mb-4" href="#plataforma">
              Ver Planes
            </v-btn>
          </div>
        </v-col>

        <v-col cols="12" md="6">
          <v-card color="#091A35" rounded="xl" outlined class="pa-4 pa-md-6">
            <v-row align="center" class="mb-4">
              <v-col cols="auto">
                <v-avatar size="72" tile>
                  <v-img src="/branding/isotipo-logistikpro.png" contain />
                </v-avatar>
              </v-col>

              <v-col>
                <div class="text-overline accent--text">
                  NUESTRA PLATAFORMA
                </div>
                <div class="text-h5 font-weight-bold">
                  LOGISTIKPRO
                </div>
              </v-col>
            </v-row>

            <v-sheet color="#0D2347" rounded="lg" class="pa-5 mb-4">
              <v-row align="center">
                <v-col cols="12" sm="7">
                  <div class="text-subtitle-1 font-weight-medium mb-2">
                    Diseño pensado para presentar el producto
                  </div>
                  <div class="text-body-2">
                    La home se enfoca en comunicar el valor de la suite sin saturar, con jerarquías limpias y rutas
                    claras hacia cada subdominio.
                  </div>
                </v-col>

                <v-col cols="12" sm="5">
                  <v-img src="/branding/logo-logistikpro.png" contain max-height="150" />
                </v-col>
              </v-row>
            </v-sheet>

            <v-row>
              <v-col v-for="card in heroCards" :key="card.title" cols="12" sm="4">
                <v-card rounded="lg" color="#0B1D3A" outlined class="pa-4 fill-height">
                  <v-icon :color="card.color" class="mb-3">
                    {{ card.icon }}
                  </v-icon>
                  <div class="text-subtitle-1 font-weight-medium mb-2">
                    {{ card.title }}
                  </div>
                  <div class="text-body-2">
                    {{ card.text }}
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-sheet id="plataforma" color="#07162d" class="py-10 py-md-16">
      <v-container>
        <v-row align="end" class="mb-6">
          <v-col cols="12" md="8">
            <div class="text-overline accent--text mb-2">
              Plataforma
            </div>
            <h2 class="text-h4 font-weight-bold mb-3">
              Módulos para informar, operar y medir sin saturar la página.
            </h2>
            <div class="text-body-1">
              Cada bloque de contenido debe ser fácil de leer y fácil de convertir en pantallas más adelante.
            </div>
          </v-col>
        </v-row>

        <v-row>
          <v-col v-for="item in modules" :key="item.title" cols="12" sm="6" lg="3">
            <v-card rounded="xl" outlined color="#0B1D3A" class="pa-5 fill-height">
              <v-icon :color="item.color" size="32" class="mb-4">
                {{ item.icon }}
              </v-icon>
              <div class="text-h6 font-weight-bold mb-3">
                {{ item.title }}
              </div>
              <div class="text-body-2 mb-4">
                {{ item.text }}
              </div>
              <v-list dense color="transparent" class="pa-0">
                <v-list-item v-for="line in item.lines" :key="line" class="px-0">
                  <v-list-item-icon class="mr-3">
                    <v-icon small color="accent">
                      mdi-minus
                    </v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title class="text-body-2">
                      {{ line }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>

    <v-container id="planes" class="py-12 py-md-16">
      <v-row align="end" class="mb-6">
        <v-col cols="12" md="8">
          <div class="text-overline accent--text mb-2">
            Planes
          </div>
          <h2 class="text-h4 font-weight-bold mb-3">
            Estructura de planes lista para personalizar, sin valores quemados.
          </h2>
          <div class="text-body-1">
            Esta maqueta deja el espacio para que luego definamos módulos, permisos y cobertura de cada oferta.
          </div>
        </v-col>
      </v-row>

      <v-row>
        <v-col v-for="plan in plans" :key="plan.title" cols="12" md="4">
          <v-card rounded="xl" outlined class="pa-6 fill-height" :color="plan.featured ? '#10274D' : '#091A35'">
            <v-chip v-if="plan.badge" color="accent" dark label small class="mb-4">
              {{ plan.badge }}
            </v-chip>

            <div class="text-h5 font-weight-bold mb-2">
              {{ plan.title }}
            </div>
            <div class="text-body-2 mb-4">
              {{ plan.text }}
            </div>

            <v-divider class="my-4" />

            <v-list dense color="transparent" class="pa-0">
              <v-list-item v-for="line in plan.lines" :key="line" class="px-0">
                <v-list-item-icon class="mr-3">
                  <v-icon small color="accent">
                    mdi-check
                  </v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title class="text-body-2">
                    {{ line }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>

            <v-btn block rounded :color="plan.featured ? 'accent' : 'transparent'" :outlined="!plan.featured"
              :dark="plan.featured" class="mt-6" href="#contacto">
              Quiero este enfoque
            </v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-sheet id="subdominios" color="#07162d" class="py-10 py-md-16">
      <v-container>
        <v-row align="end" class="mb-6">
          <v-col cols="12" md="8">
            <div class="text-overline accent--text mb-2">
              Subdominios
            </div>
            <h2 class="text-h4 font-weight-bold mb-3">
              Una web para la marca, con caminos separados para cada público.
            </h2>
            <div class="text-body-1">
              La arquitectura queda lista para una página pública, el panel superadmin y el portal de clientes sin
              mezclar experiencias.
            </div>
          </v-col>
        </v-row>

        <v-row>
          <v-col v-for="zone in zones" :key="zone.title" cols="12" md="4">
            <v-card rounded="xl" outlined color="#0B1D3A" class="pa-6 fill-height">
              <v-icon :color="zone.color" size="32" class="mb-4">
                {{ zone.icon }}
              </v-icon>

              <div class="text-h5 font-weight-bold mb-2">
                {{ zone.title }}
              </div>

              <div class="text-body-2 mb-4">
                {{ zone.text }}
              </div>

              <v-chip v-for="tag in zone.tags" :key="tag" label small outlined class="mr-2 mb-2">
                {{ tag }}
              </v-chip>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>

    <v-container id="contacto" class="py-12 py-md-16">
      <v-card rounded="xl" color="#10274D" outlined class="pa-5 pa-md-10">
        <v-row align="center" justify="space-between">
          <v-col cols="12" md="8">
            <div class="text-overline accent--text mb-2">
              Contacto
            </div>
            <h2 class="text-h4 font-weight-bold mb-3">
              Dejamos la maqueta lista para seguir aterrizando pantallas y contenido.
            </h2>
            <div class="text-body-1">
              Si quieres, en el siguiente paso armamos el homepage final, la navegación por subdominios y el sistema
              visual completo dentro de Vuetify 2.
            </div>
          </v-col>

          <v-col cols="12" md="4" class="text-center text-md-right">
            <v-btn color="accent" dark x-large rounded block class="mb-4">
              Agendar demo
            </v-btn>
            <v-btn outlined x-large rounded block>
              Revisar contenido
            </v-btn>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </div>
</template>

<script>
export default {
  name: 'IndexPage',
  data() {
    return {
      heroPoints: [
        {
          title: 'Gestión centralizada',
          subtitle: 'Administra tu negocio desde un solo lugar',
          text: 'Ventas, inventario, clientes y operaciones conectados en una plataforma.',
          icon: 'mdi-view-dashboard-outline',
          color: 'info'
        },
        {
          title: 'Control en tiempo real',
          subtitle: 'Ten el control de tu operación',
          text: 'Consulta información clave de tu negocio y toma decisiones con mayor rapidez.',
          icon: 'mdi-timer-sand',
          color: 'warning'
        },
        {
          title: 'Escalable y modular',
          subtitle: 'Crece al ritmo de tu negocio',
          text: 'Activa nuevas funcionalidades y adapta LogistikPro a las necesidades de tu empresa.',
          icon: 'mdi-puzzle-outline',
          color: 'success'
        },
        {
          title: 'Crecimiento continuo',
          subtitle: 'Mejora tu operación sin rehacer todo',
          text: 'Suma capacidades nuevas a medida que tu empresa crece y cambia.',
          icon: 'mdi-rocket-launch-outline',
          color: 'primary'
        }
      ],
      heroCards: [
        {
          title: 'Web pública',
          text: 'Presenta la marca, el mensaje y la propuesta de valor de LogistikPro, con foco en atraer nuevos clientes.',
          icon: 'mdi-web',
          color: 'info'
        },
        {
          title: 'Clientes',
          text: 'Una experiencia orientada al uso del software, donde cada cliente puede gestionar y consultar las funcionalidades disponibles para su negocio.',
          icon: 'mdi-account-multiple',
          color: 'success'
        },
        {
          title: 'Gestión empresarial',
          text: 'Herramientas diseñadas para facilitar la gestión diaria, optimizar procesos y mantener el control de la operación.',
          icon: 'mdi-chart-bar-stacked',
          color: 'warning'
        }
      ],
      modules: [
        {
          title: 'Operación',
          text: 'Módulos para centralizar la gestión diaria de la plataforma.',
          icon: 'mdi-cog-outline',
          color: 'info',
          lines: ['Flujos claros', 'Navegación por módulos', 'Escalable por sedes']
        },
        {
          title: 'Control',
          text: 'Estructura para permisos, roles y decisiones desde un solo lugar.',
          icon: 'mdi-shield-account-outline',
          color: 'warning',
          lines: ['Acceso segmentado', 'Gobierno por perfiles', 'Visibilidad por subdominio']
        },
        {
          title: 'Métricas',
          text: 'Bloques preparados para dashboards y seguimiento visual del negocio.',
          icon: 'mdi-chart-line',
          color: 'success',
          lines: ['Lectura rápida', 'Indicadores configurables', 'Enfoque medible']
        },
        {
          title: 'Crecimiento',
          text: 'Una base que permite sumar módulos sin rehacer la experiencia completa.',
          icon: 'mdi-rocket-launch-outline',
          color: 'primary',
          lines: ['Arquitectura modular', 'Expansión por etapas', 'Imagen de marca sólida']
        }
      ],
      plans: [
        {
          title: 'Inicio',
          text: 'Para presentar la plataforma y dejar claros los módulos base.',
          badge: 'Base',
          featured: false,
          lines: ['Landing limpia y entendible', 'Mensajería de marca', 'Ruta a contacto']
        },
        {
          title: 'Crecimiento',
          text: 'Pensado para empresas que necesitan más separación y flujo comercial.',
          badge: 'Recomendado',
          featured: true,
          lines: ['Subdominios diferenciados', 'Bloques para módulos', 'Arquitectura preparada para expansión']
        },
        {
          title: 'Escala',
          text: 'Orientado a una suite completa con más alcance operativo y gobierno.',
          badge: 'Avanzado',
          featured: false,
          lines: ['Mayor cobertura visual', 'Secciones adaptables', 'Preparado para futuros paneles']
        }
      ],
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
  }
}
</script>

<style>
.landing-page {
  color: #f4f7fb;
}

.landing-title {
  font-size: clamp(2.4rem, 5vw, 4.6rem);
  line-height: 1.02;
  letter-spacing: -0.03em;
}

.landing-page .v-sheet,
.landing-page .v-card {
  border-color: rgba(255, 255, 255, 0.08) !important;
}

.landing-page .v-card,
.landing-page .v-sheet {
  box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
}

.hero-point-card {
  border: 1px solid rgba(255, 255, 255, 0.08);
  min-height: 176px;
  height: 100%;
  transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}

.hero-point-card:hover {
  transform: translateY(-2px);
  border-color: rgba(244, 182, 64, 0.35);
  box-shadow: 0 20px 34px rgba(0, 0, 0, 0.28);
}

.hero-point-title,
.hero-point-subtitle,
.hero-point-text {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.hero-point-title {
  -webkit-line-clamp: 1;
}

.hero-point-subtitle {
  -webkit-line-clamp: 1;
}

.hero-point-text {
  -webkit-line-clamp: 3;
}

@media (max-width: 599px) {
  .hero-point-card {
    min-height: 156px;
  }
}

@media (max-width: 959px) {

  .landing-page .v-card,
  .landing-page .v-sheet {
    border-radius: 20px;
  }
}

@media (max-width: 599px) {
  .landing-title {
    font-size: clamp(2rem, 10vw, 2.8rem);
  }
}
</style>
