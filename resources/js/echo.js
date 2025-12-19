import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Get the API base URL (same as in api.js)
const apiBaseURL = 'http://127.0.0.1:8000/api';

// Function to get auth headers
const getAuthHeaders = () => {
    const token = localStorage.getItem('token');
    return {
        Authorization: token ? `Bearer ${token}` : '',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
};

// Initialize Echo with Reverb
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
const useTLS = reverbScheme === 'https';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: reverbHost,
    wsPort: reverbPort,
    wssPort: useTLS ? (import.meta.env.VITE_REVERB_WSS_PORT || 443) : reverbPort,
    forceTLS: useTLS,
    enabledTransports: useTLS ? ['wss'] : ['ws'],
    authEndpoint: `${apiBaseURL}/broadcasting/auth`,
    auth: {
        headers: getAuthHeaders(),
    },
    disableStats: true,
});

// Export function to reconnect Echo with new token
window.reconnectEcho = () => {
    if (window.Echo) {
        window.Echo.disconnect();
    }
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
    const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
    const reverbPort = import.meta.env.VITE_REVERB_PORT || 8080;
    const useTLS = reverbScheme === 'https';

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: useTLS ? (import.meta.env.VITE_REVERB_WSS_PORT || 443) : reverbPort,
        forceTLS: useTLS,
        enabledTransports: useTLS ? ['wss'] : ['ws'],
        authEndpoint: `${apiBaseURL}/broadcasting/auth`,
        auth: {
            headers: getAuthHeaders(),
        },
        disableStats: true,
    });
};

