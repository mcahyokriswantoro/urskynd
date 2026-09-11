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
            colors: {
                brand: {
                    cream: '#F8F2EC',
                    creamAlt: '#F4EAE1',
                    creamLight: '#FFFDFC',
                    primary: '#9A6A52',
                    primaryAlt: '#A97A61',
                    primaryLight: '#B98C73',
                    secondary: '#D8C0AF',
                    secondaryLight: '#E9DDD3',
                    text: '#352922',
                    textAlt: '#5D4C43',
                    border: '#E7DBD1',
                    success: '#73926F',
                    warning: '#C49B5F',
                    danger: '#B85D57',
                }
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            boxShadow: {
                'soft': '0 4px 20px -2px rgba(154, 106, 82, 0.08)',
                'soft-lg': '0 10px 30px -5px rgba(154, 106, 82, 0.12)',
            }
        },
    },

    plugins: [forms],
};
