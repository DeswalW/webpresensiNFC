/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          green: '#289341',
          yellow: '#FFF212',
          black: '#373435',
        },
        'primary-green': '#289341',
        'primary-yellow': '#FFF212',
        'primary-black': '#373435',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}

