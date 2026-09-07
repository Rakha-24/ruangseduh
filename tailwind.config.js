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
                // Cream / off-white — background utama yang hangat
                cream: {
                    50: '#FDFBF7',
                    100: '#FAF4EC',
                    200: '#F3E8DA',
                    300: '#EADCC8',
                },
                // Terracotta — aksen utama (CTA, harga, highlight)
                terracotta: {
                    300: '#E8A87C',
                    400: '#D97B54',
                    500: '#C4622D',
                    600: '#A84A22',
                    700: '#8A3B1C',
                },
                // Coffee (coklat tua) — teks & elemen framing
                coffee: {
                    100: '#E8DCCF',
                    400: '#7A5639',
                    600: '#5C3D2E',
                    700: '#4A3728',
                    800: '#33261B',
                    900: '#211915',
                },
                // Sage green — aksen sekunder / elemen sukses
                sage: {
                    100: '#E3EAD9',
                    200: '#C9D6B8',
                    300: '#AFC29A',
                    400: '#8FA879',
                    500: '#7D9177',
                    600: '#5F7054',
                },
            },

            fontFamily: {
                // Serif untuk Heading — Playfair Display (fallback Merriweather)
                serif: ['"Playfair Display"', 'Merriweather', 'Georgia', 'serif', ...defaultTheme.fontFamily.serif],
                // Sans-serif untuk Body — Inter
                sans: ['Inter', 'Poppins', ...defaultTheme.fontFamily.sans],
            },

            boxShadow: {
                warm: '0 10px 30px -12px rgba(74, 55, 40, 0.25)',
            },

            borderRadius: {
                cozy: '0.75rem',
            },
        },
    },

    plugins: [forms],
};