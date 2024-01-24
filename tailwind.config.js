import colors from "tailwindcss/colors.js";

/** @type {import('tailwindcss').Config} */
// const colors = require('tailwindcss/colors');
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                "primary": colors.blue,
            },
            animation: {
                'pulse-fast': 'pulse 0.8s linear infinite',
            }
        },
    },
    plugins: [],
}

