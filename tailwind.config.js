/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/filament/**/*.blade.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './resources/css/**/*.css',
  ],

  // ⬇️ Masukkan di sini
  safelist: [
    'bg-bee-light',
    'bg-bee-dark',
    'bg-bee-yellow',
    'bg-bee-primary',
    'hover:bg-bee-accent',
    'text-bee-white',
    'text-bee-black',
    'text-bee-yellow',
  ],

  theme: {
    extend: {
      colors: {
        'bee-light': '#fffaeb',
        'bee-yellow': '#ffcd3f',
        'bee-dark': '#2b2b2b',
        'bee-text': '#202326',
        'bee-primary': '#e5b400',
        'bee-secondary': '#f4dc96',
        'bee-accent': '#ffb742',
        'bee-white': '#ffffff',
        'bee-black': '#000000',
        'bee-footer': '#f4dc96',
      },
    },
  },

  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/container-queries'),
  ],
};