<template>
  <v-app dark class="logistik-app">
    <v-navigation-drawer v-model="drawer" temporary app right color="#07162D" width="300">
      <div class="pa-6">
        <v-img src="/branding/isotipo-logistikpro.png" contain max-width="92" class="mx-auto" />
      </div>

      <v-list nav dense color="transparent">
        <v-list-item v-for="item in navItems" :key="item.text" :href="item.href" @click="drawer = false">
          <v-list-item-content>
            <v-list-item-title>
              {{ item.text }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <div class="pa-6">
        <v-btn block color="accent" dark x-large rounded href="#contacto">
          Solicitar demo
        </v-btn>
      </div>
    </v-navigation-drawer>

    <v-app-bar app flat color="#07162D" class="px-0">
      <v-container class="py-0">
        <v-row align="center" no-gutters class="flex-nowrap">
          <v-col cols="auto">
            <a href="#inicio" class="text-decoration-none">
              <v-img src="/branding/logo-logistikpro.png" contain max-width="200" class="logistik-brand" />
            </a>
          </v-col>

          <v-spacer />

          <v-col cols="auto" class="d-none d-md-flex">
            <v-btn v-for="item in navItems" :key="item.text" text small class="mx-1" :href="item.href">
              {{ item.text }}
            </v-btn>
          </v-col>

          <v-col cols="auto" class="d-flex align-center ml-2 ml-sm-4">
            <v-btn outlined rounded class="mr-3 d-none d-sm-flex" href="#plataforma">
              VER PLANES
            </v-btn>

            <v-btn color="accent" dark rounded class="d-none d-sm-flex" href="#contacto">
              Solicitar demo
            </v-btn>

            <v-btn icon class="ml-2 d-md-none" @click.stop="drawer = !drawer">
              <v-icon>mdi-menu</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-app-bar>

    <v-main>
      <Nuxt />
    </v-main>

    <v-footer color="#061327" padless>
      <v-container class="py-6">
        <v-row align="center" justify="space-between">
          <v-col cols="12" md="6">
            <div class="text-subtitle-1 font-weight-medium">
              LogistikPro
            </div>
            <div class="text-body-2">
              Web pública, superadmin y clientes pensados por subdominios, con una identidad visual coherente en toda la
              suite.
            </div>
          </v-col>

          <v-col cols="12" md="5" class="text-md-right">
            <div class="text-body-2">
              Plataforma modular para operación, control y crecimiento.
            </div>
          </v-col>
        </v-row>
      </v-container>
    </v-footer>
  </v-app>
</template>

<script>
export default {
  name: 'DefaultLayout',
  data() {
    return {
      drawer: false,
      navItems: [
        { text: 'Inicio', href: '#inicio' },
        { text: 'Plataforma', href: '#plataforma' },
        { text: 'Planes', href: '#planes' },
        { text: 'Contacto', href: '#contacto' }
      ]
    }
  }
}
</script>

<style>
html,
body,
#__nuxt,
#__layout {
  background: #07162d;
}

.v-application,
.v-application--wrap,
.v-main,
.v-main__wrap {
  background: #07162d !important;
}

.logistik-app {
  background: #07162d;
}

.logistik-app .v-main {
  background: transparent;
}

.logistik-brand {
  width: 190px;
}

@media (max-width: 599px) {
  .logistik-brand {
    width: 136px;
  }
}
</style>
