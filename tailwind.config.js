import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Exo', ...defaultTheme.fontFamily.sans],
            },
            dark: {
                primary: '#2A0F2E',
                secondary: '#541F5C',
            },
            backgroundColor: {
                primary: '#141316',
                secondary: '#249E81',
                'light-frost': '#F0EFF1',
                'grayish-blue': '#2D2B31',
                'turquoise': '#37C3A2',
                'grayish': '#D3D1D7',
                'rich-black': '#19181B',
                'light-gray': '#F7F7F8',
                'charcoal-gray': '#201F23',
                'ghost-white': '#FCFCFD',
                'shade-green': '#152824',
            },
            textColor: {
                primary: '#ACA8B3',
                secondary: '#666170',
                'light-frost': '#F0EFF1',
                'jet-black': '#141316',
                'gray-tones': '#E5E4E7',
                'rich-black': '#19181B',
                'ghost-white': '#FCFCFD',
            },
            colors: {
                'primary-dark': '#141316',
                'green': '#3FE4BD',
                'primary-white': '#F0EFF1',
                'dark-green': '#187C65',
                'charcoal-gray': '#201F23',
                'gray-tones': '#E5E4E7',
                'lavender-gray': '#ACA8B3',
                'shade-green': '#152824',
                'grayish-blue': '#2D2B31',
                'primary-grayish': '#D3D1D7',
                'primary-text': '#19181B'
            }
        },
    },

    plugins: [forms, typography],
};
