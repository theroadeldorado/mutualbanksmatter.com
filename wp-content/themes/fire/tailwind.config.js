// Default Config: https://github.com/tailwindcss/tailwindcss/blob/master/stubs/defaultConfig.stub.js
module.exports = {
  content: ['./templates/**/*.php', './templates/**/*.js', './theme/assets/**/*.js', './theme/main.js', './*.php', './inc/**/*.php', './acf-json/**/*.json'],
  safelist: [
    {
      pattern: /(mt|mb)-gap-(0|xs|sm|md|lg|xl)/,
      variants: ['lg', 'md'],
    },
  ],
  theme: {
    extend: {
      spacing: {
        'gap-0': 'var(--spacing-gap-0)',
        'gap-xs': 'var(--spacing-gap-xs)',
        'gap-sm': 'var(--spacing-gap-sm)',
        'gap-md': 'var(--spacing-gap-md)',
        'gap-lg': 'var(--spacing-gap-lg)',
        'gap-xl': 'var(--spacing-gap-xl)',
      },
    },
  },
};
