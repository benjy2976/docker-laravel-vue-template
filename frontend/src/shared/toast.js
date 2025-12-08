import { Toast } from 'bootstrap'

let container = null

const ensureContainer = () => {
  if (!container) {
    container = document.createElement('div')
    container.className = 'toast-container position-fixed top-0 end-0 p-3'
    container.style.zIndex = 2000
    document.body.appendChild(container)
  }
}

const showToast = (message, variant = 'primary') => {
  ensureContainer()
  const el = document.createElement('div')
  el.className = `toast align-items-center text-bg-${variant} border-0`
  el.setAttribute('role', 'alert')
  el.setAttribute('aria-live', 'assertive')
  el.setAttribute('aria-atomic', 'true')
  el.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `
  container.appendChild(el)

  const toast = new Toast(el, { delay: 3000 })
  toast.show()
  el.addEventListener('hidden.bs.toast', () => {
    el.remove()
  })
}

export const useToast = () => ({
  success : (msg) => showToast(msg, 'success'),
  info    : (msg) => showToast(msg, 'info'),
  warning : (msg) => showToast(msg, 'warning'),
  danger  : (msg) => showToast(msg, 'danger'),
})
