import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import os from 'os';

// Dynamically retrieve the current active local IPv4 address
function getLocalIpAddress() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const net of interfaces[name]) {
            // Find a non-internal IPv4 address
            if (net.family === 'IPv4' && !net.internal) {
                return net.address;
            }
        }
    }
    return 'localhost';
}

const activeIp = getLocalIpAddress();

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: activeIp,
        hmr: {
            host: activeIp,
        }
    },
});
