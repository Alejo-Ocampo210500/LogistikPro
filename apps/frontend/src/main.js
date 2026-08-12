import Vue from 'vue'
import App from './App.vue'
import vuetify from './plugins/vuetify'

import '@mdi/font/css/materialdesignicons.css'
import './assets/styles/app.css'

Vue.config.productionTip = false

const sanitizeDecimalValue = (rawValue = '') => {
  const normalized = String(rawValue).replace(/,/g, '.').replace(/[^\d.]/g, '')
  const firstDotIndex = normalized.indexOf('.')

  if (firstDotIndex < 0) {
    return normalized
  }

  return `${normalized.slice(0, firstDotIndex + 1)}${normalized.slice(firstDotIndex + 1).replace(/\./g, '')}`
}

const applyInputEnhancements = (rootElement) => {
  if (!rootElement || !(rootElement instanceof Element)) {
    return
  }

  rootElement.querySelectorAll('input').forEach((input) => {
    if (input.dataset.enhancedByApp === 'true') {
      return
    }

    input.dataset.enhancedByApp = 'true'

    if (input.type === 'date') {
      const openNativeDatePicker = () => {
        if (typeof input.showPicker === 'function') {
          try {
            input.showPicker()
          } catch (error) {
            // Ignore unsupported picker errors and keep native behavior.
          }
        }
      }

      input.addEventListener('focus', openNativeDatePicker)
      input.addEventListener('click', openNativeDatePicker)
    }

    const isStrictNumeric = input.type === 'tel' || input.dataset.onlyNumeric === 'true'
    const isDecimalNumeric = input.type === 'number'

    if (!isStrictNumeric && !isDecimalNumeric) {
      return
    }

    input.addEventListener('input', () => {
      const sanitizedValue = isDecimalNumeric
        ? sanitizeDecimalValue(input.value)
        : String(input.value || '').replace(/\D+/g, '')

      if (sanitizedValue === input.value) {
        return
      }

      input.value = sanitizedValue
      input.dispatchEvent(new Event('input', { bubbles: true }))
    })
  })
}

Vue.mixin({
  mounted() {
    this.$nextTick(() => {
      applyInputEnhancements(this.$el)
    })
  },
  updated() {
    this.$nextTick(() => {
      applyInputEnhancements(this.$el)
    })
  },
})

new Vue({
  vuetify,
  render: h => h(App),
}).$mount('#app')