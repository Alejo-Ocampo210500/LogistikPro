export default {
  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: '%s | LogistikPro',
    title: 'LogistikPro',
    htmlAttrs: {
      lang: 'es'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      {
        hid: 'description',
        name: 'description',
        content: 'LogistikPro: plataforma modular para informar, operar y escalar software por subdominios.'
      },
      { name: 'format-detection', content: 'telephone=no' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    '~/plugins/api.js',
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/vuetify
    '@nuxtjs/vuetify',
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/proxy',
  ],

  axios: {
    baseURL: process.env.API_BASE_URL || process.env.VUE_APP_API_BASE_URL || 'http://127.0.0.1:8000/api',
    browserBaseURL: process.env.API_BROWSER_BASE_URL || '/api',
    proxy: true,
    headers: {
      common: {
        Accept: 'application/json',
      },
    },
  },

  proxy: {
    '/api/': {
      target: process.env.API_PROXY_TARGET || 'http://127.0.0.1:8000',
      changeOrigin: true,
    },
  },

  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    theme: {
      dark: true,
      themes: {
        dark: {
          primary: '#143B7A',
          accent: '#F4B640',
          secondary: '#0D1F3D',
          info: '#4AA3FF',
          warning: '#F7C66C',
          error: '#FF6B6B',
          success: '#49C18F'
        }
      }
    }
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
    transpile: ['vuetify'],
  },

  // Watcher settings: improve HMR reliability in mounted/virtualized filesystems
  watchers: {
    webpack: {
      poll: 1000
    },
    chokidar: {
      usePolling: true,
      interval: 1000
    }
  }
}
