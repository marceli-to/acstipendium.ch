/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

/** 
 * Generate spacing values from 0 to 500px with 0.0625rem increments
 * 
 * @type {Object}
 */
const spacing_max = 501;
const spacing = Object.fromEntries(
  Array.from({ length: spacing_max }, (_, i) => {
    const remValue = (i * 0.0625).toFixed(4).replace(/\.?0+$/, '')
    return [i, `${remValue}rem`]
  })
)

export default {
  content: [
    './resources/**/*.antlers.html',
    './resources/**/*.antlers.php',
    './resources/**/*.blade.php',
    './resources/**/*.vue',
    './resources/**/*.js',
    './content/**/*.md'
  ],

  safelist: [

  ],

  theme: {

    extend: {

      screens: {
        'xs': '480px',
      },

      maxWidth: {
        'container': '90rem', // 1280px
      },

      backgroundImage: {
        'header-gradient': 'linear-gradient(180deg, rgb(var(--color-secondary) / 1) 40%, rgb(var(--color-secondary) / 0) 100%)',
        'menu-gradient': 'linear-gradient(180deg, rgb(var(--color-secondary) / 1) 40%, rgb(var(--color-secondary) / 0) 100%)',
      },
     
      colors: {
        primary: 'rgb(var(--color-primary) / <alpha-value>)',
        secondary: 'rgb(var(--color-secondary) / <alpha-value>)',
      },

      fontFamily: {
        'btcircuit': ['BTCircuit-Regular', ...defaultTheme.fontFamily.sans],
      },

      fontSize: {
        'xxs': '0.625rem',  // 10px
        'xs':  '0.75rem',   // 12px
        'sm':  '0.875rem',  // 14px
        'md':  '1rem',      // 16px
        'lg':  '1.25rem',   // 20px
        'xl':  '1.5rem',    // 24px
        '2xl': '2rem',      // 32px
        '3xl': '3rem',      // 48px
      },
      
      zIndex: {
        '60': 60,
        '70': 70,
        '80': 80,
        '90': 90,
        '100': 100,
        '150': 150,
        '200': 200,
      },
    },

    spacing

  },

  plugins: [
    require('@tailwindcss/typography'),
  ],
};
