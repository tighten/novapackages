const { colors } = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    screens: {
      sm: '576px',
      md: '992px',
      lg: '1200px',
      xl: '1440px'
    },
    extend: {
      colors: {
        'custom-indigo-light': '#e6e8ff',
        'custom-indigo-darker': '#2f365f',
        'custom-indigo-darkest': '#2b3158',

        'brand-lighter': '#4299e1',
        'brand-light': '#63b3ed',
        'brand': '#4299e1',
        'brand-dark': '#63b3ed',
        'brand-darker': '#2c5282',

        // Overwrite new colors with old colors
        'gray-200': '#edf2f7',

          /** This should work but :shrug: */
          // 'brand-lighter': colors.blue[200],
          // 'brand-light': colors.blue[400],
          // 'brand': colors.blue[500],
          // 'brand-dark': colors.blue[600],
          // 'brand-darker': colors.blue[800],
      },
      spacing: {
        '9': '2.25rem',
      }
    },
  },
  plugins: [],
};
