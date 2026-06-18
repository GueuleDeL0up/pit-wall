/* ================================================================
   app.js — Horloge
================================================================ */

'use strict';

const pad = n => String(n).padStart(2, '0');
const tnow = () => {
  const d = new Date();
  return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
};
const clockEl = document.getElementById('clock');
clockEl.textContent = tnow();
setInterval(() => { clockEl.textContent = tnow(); }, 1000);

// Exposer pour les autres modules
window.App = { tnow };
