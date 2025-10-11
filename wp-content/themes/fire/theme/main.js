import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
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
  Alpine.plugin(focus);
  Alpine.plugin(collapse);
  Alpine.start();
});
