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
    extend: {},
  },
  plugins: [],
};