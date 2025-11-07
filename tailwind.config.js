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
    { pattern: /^col-span-(1[0-2]|[1-9])$/ , variants: ['md', 'lg'] },
    { pattern: /^col-start-(1[0-2]|[1-9])$/ , variants: ['md', 'lg'] },
    { pattern: /^bg-(primary|secondary)$/ , variants: ['md', 'lg'] },
  ],

  theme: {

    extend: {

      boxShadow: {
        'glow-sm-white': '0 0 20px 4px white',
        'glow-sm-primary': '0 0 20px 4px rgb(var(--color-primary))',
        'glow-lg-white': '0 0 40px 24px white',
        'glow-lg-primary': '0 0 40px 24px rgb(var(--color-primary))',
      },

      screens: {
        'md': '56.25rem',  // 900px
        'md-wide': '65.625rem', // 1050px
        'lg': '78.125rem', // 1250px
      },

      maxWidth: {
        'container': '90rem', // 1440px
        'prose': '70ch', // 70 characters
      },

      backgroundImage: {
        'header-gradient': 'linear-gradient(180deg, rgb(var(--color-secondary) / 1) 60%, rgb(var(--color-secondary) / 0) 100%)',
        'menu-gradient': 'linear-gradient(180deg, rgb(var(--color-secondary) / 1) 40%, rgb(var(--color-secondary) / 0) 100%)',
      },
     
      colors: {
        primary: 'rgb(var(--color-primary) / <alpha-value>)',
        secondary: 'rgb(var(--color-secondary) / <alpha-value>)',
        danger: '#C83D3D',
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

      borderRadius: {
        '14': '0.875rem',  // 14px
        '16': '1rem',      // 16px
        '24': '1.5rem',    // 24px
        '32': '2rem',      // 32px
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

      keyframes: {
        'glow-pulse': {
          '0%, 100%': { boxShadow: '0 0 20px 4px white' },
          '50%': { boxShadow: '0 0 20px 4px rgba(255, 255, 255, 0.3)' },
        },
      },

      animation: {
        'glow-pulse': 'glow-pulse 1.5s ease-in-out infinite',
      },
    },

    spacing

  },

  plugins: [
    require('@tailwindcss/typography'),
  ],
};
