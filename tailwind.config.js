const { colors } = require('tailwindcss/defaultTheme')

module.exports = {
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

          'brand-lighter': colors.blue[200],
          'brand-light': colors.blue[400],
          'brand': colors.blue[500],
          'brand-dark': colors.blue[600],
          'brand-darker': colors.blue[800],
      },
    },
},
  plugins: [],
};