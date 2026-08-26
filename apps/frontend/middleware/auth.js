export default function ({ route, redirect }) {
  if (!process.client) return

  const token = sessionStorage.getItem('logistik_token')
  const path = route.path.replace(/\/+$/, '') || '/'

  if (path === '/login') {
    if (token) return redirect('/inicio/principal')
    return
  }

  if (path === '/') return

  if (!token) return redirect('/login')
}
