const TOKEN_KEY = 'logistik_token'
const USER_KEY = 'logistik_user'

export default function ({ $axios, redirect }, inject) {
  const api = $axios.create({
    headers: {
      common: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
      },
    },
  })

  const clearSession = () => {
    if (!process.client) return

    sessionStorage.removeItem(TOKEN_KEY)
    sessionStorage.removeItem(USER_KEY)
  }

  api.onRequest((config) => {
    if (!process.client) return config

    const token = sessionStorage.getItem(TOKEN_KEY)
    if (token) config.headers.common.Authorization = `Bearer ${token}`

    return config
  })

  api.onError((error) => {
    if (process.client && error.response?.status === 401) {
      clearSession()

      if (window.location.pathname !== '/login') redirect('/login')
    }

    return Promise.reject(error)
  })

  inject('api', api)
  inject('setApiSession', (token, user = {}) => {
    if (!process.client) return

    if (!token) {
      clearSession()
      return
    }

    sessionStorage.setItem(TOKEN_KEY, token)
    sessionStorage.setItem(USER_KEY, JSON.stringify(user))
  })
  inject('clearApiSession', clearSession)
  inject('getApiUser', () => {
    if (!process.client) return null

    try {
      return JSON.parse(sessionStorage.getItem(USER_KEY) || 'null')
    } catch (error) {
      clearSession()
      return null
    }
  })
}
