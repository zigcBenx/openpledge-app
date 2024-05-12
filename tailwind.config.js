import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import plugin from 'tailwindcss/plugin';

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
            colors: {
                'lavender-mist': '#F0EFF1',
                'ghost-white': '#F7F7F8',
                'seashell': '#FCFCFD',
                'spun-pearl': '#ACA8B3',
                'pale-aqua': '#D5EBE7',
                'periwinkle': '#EAD8F3',
                'thistle': '#E3BBF7',

                'green': '#3FE4BD',
                'mint-green': '#CCEFE9',
                'dark-green': '#187C65',
                'ocean-green': '#249E81',
                'shade-green': '#152824',
                'turquoise': '#37C3A2',
                
                'light-sea-shade': '#85E0CC',
                'platinum': '#E5E4E7',
                'tundora': '#666170',
                'rich-black': '#19181B',
                'mondo': '#4B4752',
                'oil': '#141316',
                'purple-heart': '#B235D4',
                'gunmetal': '#2D2B31',

                'grayish': '#D3D1D7',
                'charcoal-gray': '#201F23',
                'slate-gray': '#6b7280',

                'tropical-rain-forest': '#0D4538',
                'valhalla': '#2E1332',
                'stylish-red': '#3F1645',
                'magic-mint': '#3AD4B0',
                'icon-idle-gray': '#666666',
                'whitish-gray': '#F0EFF1',
            },
        },
    },

    plugins: [
        forms, 
        typography,
        new plugin(({ addVariant }) => {
            addVariant('search-cancel', '&::-webkit-search-cancel-button');
        }),
    ],
};
