<!-- layouts/blank.vue -->
<template>
  <v-app dark class="logistik-app">
    <v-main>
      <Nuxt />
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'BlankLayout'
}
</script>

<style>
html,
body,
#__nuxt,
#__layout,
.v-application,
.v-application--wrap,
.v-main,
.v-main__wrap {
  background: #07162d !important;
}
</style>