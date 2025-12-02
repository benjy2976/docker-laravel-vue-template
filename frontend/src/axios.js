import axios from 'axios'

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]+)'))
  return match ? decodeURIComponent(match[2]) : null
}

const api = axios.create({
  baseURL: import.meta.env.VITE_BACKEND_URL,
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
  headers: {
    'X  ': 'XMLHttpRequest',
    Accept: 'application/json',
  },
})

// Fuerza el header X-XSRF-TOKEN manualmente por si axios no lo añade.
api.interceptors.request.use((config) => {
  const token = getCookie('XSRF-TOKEN')
  if (token) {
    config.headers['X-XSRF-TOKEN'] = token
  }
  return config
})

export default api
