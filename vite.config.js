import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { exec } from 'child_process';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css', 'resources/css/filament/user/theme.css', 'resources/css/filament/public/theme.css'],
            refresh: true,
        }),
        tailwindcss(),
        {
            name: 'run-pint-on-save',
            handleHotUpdate({ file }) {
                if (file.endsWith('.php')) {
                    exec(`./vendor/bin/pint ${file}`, (error, stdout) => {
                        if (error) return;
                        if (stdout && stdout.includes('FIXED')) {
                            console.log(`\x1b[32mPint formatting applied to: ${file}\x1b[0m`);
                        }
                    });
                }
            }
        }
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
