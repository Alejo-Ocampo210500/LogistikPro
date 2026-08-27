<template>
  <v-app dark class="logistik-app">

    <HeaderLayout />

    <v-main>
      <Nuxt />
    </v-main>

  </v-app>
</template>
<script>
import HeaderLayout from './HeaderLayout.vue'
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
