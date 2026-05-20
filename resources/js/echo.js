import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

// ================= ENV CONFIG =================
const API_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'
const REVERB_HOST = import.meta.env.VITE_REVERB_HOST || window.location.hostname
const REVERB_PORT = import.meta.env.VITE_REVERB_PORT || 8080
const REVERB_KEY  = import.meta.env.VITE_REVERB_APP_KEY
const USE_TLS = import.meta.env.VITE_REVERB_SCHEME === 'https'


// ================= ECHO INIT FUNCTION =================
const initEcho = () => {
  if (window.Echo) {
    window.Echo.disconnect()
  }

  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: REVERB_KEY,
    wsHost: REVERB_HOST,
    wsPort: REVERB_PORT,
    wssPort: USE_TLS ? 443 : REVERB_PORT,
    forceTLS: USE_TLS,
    enabledTransports: USE_TLS ? ['wss'] : ['ws'],
    disableStats: true,
  })
}

// ================= INIT ON LOAD =================
initEcho()

// ================= RECONNECT (after login/logout) =================
window.reconnectEcho = () => {
  initEcho()
}
