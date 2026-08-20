<template>
  <v-app dark class="logistik-app">

    <HeaderLayout />

    <v-main>
      <Nuxt />
    </v-main>

    <v-footer color="#061327" padless>
      <v-container class="py-6">
        <v-row align="center" justify="space-between">
          <v-col cols="12" md="6">
            <div class="text-subtitle-1 font-weight-medium">
              LogistikProsss
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
import HeaderLayout from './headerLayout.vue'
export default {
  name: 'DefaultLayout',
  components: {
    HeaderLayout
  }
}
</script>
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
