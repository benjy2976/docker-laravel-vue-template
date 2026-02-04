// Devuelve la zona horaria del usuario usando el pais asociado o un fallback.
export const resolveUserTimezone = (user, countryStore, fallback = 'America/Lima') => {
  const countryId = user?.country_id
  if (!countryId || !countryStore?.find) return fallback
  const country = countryStore.find(countryId)
  const timezone = country?.timezone || ''
  return timezone || fallback
}

// Devuelve la fecha formateada en la zona horaria indicada (sin offset).
export const formatDateTime = (value, timeZone = 'America/Lima') => {
  if (!value) return '—'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return '—'
  try {
    return new Intl.DateTimeFormat('es-PE', {
      dateStyle : 'short',
      timeStyle : 'short',
      timeZone,
    }).format(date)
  } catch (error) {
    // ignore and fallback
  }
  try {
    return new Intl.DateTimeFormat('es-PE', {
      dateStyle : 'short',
      timeStyle : 'short',
    }).format(date)
  } catch (error) {
    return '—'
  }
}

// Devuelve la fecha formateada (sin hora) en la zona horaria indicada.
export const formatDateOnly = (value, timeZone = 'America/Lima') => {
  if (!value) return '—'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return '—'
  try {
    return new Intl.DateTimeFormat('es-PE', { dateStyle: 'short', timeZone }).format(date)
  } catch (error) {
    // ignore and fallback
  }
  try {
    return new Intl.DateTimeFormat('es-PE', { dateStyle: 'short' }).format(date)
  } catch (error) {
    return '—'
  }
}
