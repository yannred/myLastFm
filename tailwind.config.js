/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    // require('./assets/vendor/@popperjs/core/core.index.js'),
    // require('./assets/vendor/flowbite/flowbite.index.js'),
  ],
}
