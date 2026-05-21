import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef5ff',
                    100: '#d9e8ff',
                    200: '#b8d4f8',
                    300: '#8bb8f0',
                    400: '#5a96e8',
                    500: '#2d74e0',
                    600: '#0066ff',
                    700: '#0052cc',
                    800: '#003d99',
                    900: '#002966',
                },
            },
            boxShadow: {
                soft: '0 4px 24px rgba(0, 102, 255, 0.08)',
                card: '0 8px 32px rgba(0, 80, 180, 0.12)',
            },
        },
    },

    plugins: [forms],
};
