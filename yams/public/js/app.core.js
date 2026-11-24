/*
  app.core.js
  Helpers globales del sistema.
*/

(function () {
  'use strict';

  // Utilidades DOM
  window.qs = window.qs || function(sel, root){ return (root||document).querySelector(sel); };
  window.qsa = window.qsa || function(sel, root){ return Array.from((root||document).querySelectorAll(sel)); };

  // Fix global: backdrops/scroll al cerrar modales
  document.addEventListener('hidden.bs.modal', function () {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = 'auto';
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
  });
})();
