/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./index.html",
        "./src/**/*.{vue,js,ts,jsx,tsx}",
    ],
    darkMode: 'class',
    theme: {
        extend: {},
    },
    safelist: [
        'aspect-video',
        'aspect-square',
        'aspect-[9/16]',
    ],
    plugins: [
        require('@tailwindcss/aspect-ratio'),
    ],
}
