import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';

function getLocalIP() {
    for (const iface of Object.values(os.networkInterfaces())) {
        for (const alias of iface) {
            if (alias.family === 'IPv4' && !alias.internal && !alias.address.startsWith('169.')) {
                return alias.address;
            }
        }
    }
    return 'localhost';
}

export default defineConfig(() => {
    const localIP = getLocalIP();

    return {
        server: {
            host: '0.0.0.0',
            origin: `http://${localIP}:5173`,
        },
        plugins: [
            tailwindcss(),
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
    };
});
