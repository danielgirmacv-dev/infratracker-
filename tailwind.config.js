import forms from '@tailwindcss/forms';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#ecfeff',
                    100: '#cffafe',
                    400: '#22d3ee',
                    500: '#1cb4c9',
                    600: '#0b9eb3',
                    700: '#0e7490',
                },
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.25rem',
            },
            boxShadow: {
                enterprise: '0 1px 3px rgba(15, 23, 42, 0.05), 0 8px 24px rgba(15, 23, 42, 0.06)',
                'enterprise-lg': '0 12px 40px rgba(15, 23, 42, 0.1)',
            },
        },
    },
    plugins: [forms],
};
