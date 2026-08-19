export default function ({ $axios }, inject) {
  const api = $axios.create({
    headers: {
      common: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    },
  })

  api.onRequest((config) => {
    if (process.client) {
      const token = localStorage.getItem('auth_token')
      if (token) {
        config.headers.common.Authorization = `Bearer ${token}`
      }
    }
  })

  api.onError((error) => {
    if (error.response && error.response.status === 401) {
      if (process.client) {
        localStorage.removeItem('auth_token')
      }
    }
  })

  inject('api', api)
  inject('setApiToken', (token) => {
    if (!process.client) {
      return
    }

    if (token) {
      localStorage.setItem('auth_token', token)
    } else {
      localStorage.removeItem('auth_token')
    }
  })
}
