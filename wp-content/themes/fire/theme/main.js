import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import 'lazysizes';
import 'lazysizes/plugins/aspectratio/ls.aspectratio.js';

// https://alpinejs.dev/globals/alpine-data#registering-from-a-bundle
document.addEventListener('alpine:init', () => {
  // stores
  // components
});

document.addEventListener('DOMContentLoaded', () => {
  window.Alpine = Alpine;
  // plugins
  Alpine.plugin(persist);
  Alpine.start();
});
