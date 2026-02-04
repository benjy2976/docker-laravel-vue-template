import api from '@/axios'

/**
 * Devuelve una URL valida a partir de un registro de archivo o string.
 *
 * @param {object|string|null} file Archivo o valor raw.
 * @returns {string} URL lista para usar en img/link.
 */
export function resolveFileUrl(file) {
  // Paso 1: resolver valor base
  if (!file) return ''
  const url = typeof file === 'string' ? file : file.url
  const base = (api.defaults.baseURL || '').replace(/\/+$/, '')
  const apiBase = base.endsWith('/api') ? base : `${base}/api`

  // Paso 2: preferir endpoint de descarga cuando hay ID
  if (file?.id) {
    return base ? `${apiBase}/files/${file.id}/download` : `/api/files/${file.id}/download`
  }

  // Paso 3: normalizar URLs absolutas/relativas
  if (/^https?:\/\//i.test(url)) {
    if (!base || url.startsWith(base)) return url
    const storageIndex = url.indexOf('/storage/')
    if (storageIndex !== -1) {
      return `${base}${url.slice(storageIndex)}`
    }
    return url
  }
  if (!base) return url
  if (url.startsWith('/')) return `${base}${url}`
  return `${base}/${url}`
}
