/* ================================================================
   app.js — Navigation, mode quotidien/course, horloge
================================================================ */

'use strict';

// ---- Horloge ----
const pad = n => String(n).padStart(2, '0');
const tnow = () => {
  const d = new Date();
  return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
};
const clockEl = document.getElementById('clock');
clockEl.textContent = tnow();
setInterval(() => { clockEl.textContent = tnow(); }, 1000);

// ---- Onglets ----
const tabs   = document.querySelectorAll('.tab');
const panels = document.querySelectorAll('.panel');

document.getElementById('tabs').addEventListener('click', e => {
  const btn = e.target.closest('.tab');
  if (!btn) return;

  tabs.forEach(t => t.classList.remove('active'));
  panels.forEach(p => p.classList.remove('active'));

  btn.classList.add('active');
  document.getElementById(btn.dataset.tab)?.classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });

  // Initialiser la voiture 3D au premier affichage
  if (btn.dataset.tab === 'voiture' && typeof window.initAlpineCar === 'function') {
    setTimeout(window.initAlpineCar, 60);
  }
});

// ---- Sélecteur de mode ----
let currentMode = 'daily';

document.getElementById('modeSwitch').addEventListener('click', e => {
  const btn = e.target.closest('button');
  if (!btn) return;

  currentMode = btn.dataset.mode;

  document.querySelectorAll('#modeSwitch button').forEach(b =>
    b.classList.toggle('on', b === btn)
  );

  document.body.classList.toggle('race-mode', currentMode === 'race');

  document.getElementById('brandSub').textContent =
    currentMode === 'race'
      ? 'Mode course · live'
      : 'Mode quotidien · capteur';

  // Basculer les stats pilotes
  document.querySelectorAll('.daily-stats').forEach(el =>
    el.style.display = currentMode === 'race' ? 'none' : 'grid'
  );
  document.querySelectorAll('.race-stats').forEach(el =>
    el.style.display = currentMode === 'race' ? 'grid' : 'none'
  );

  // Si on quitte le mode course et qu'on est sur Pitwall → revenir aux capteurs
  if (currentMode === 'daily') {
    const active = document.querySelector('.panel.active');
    if (active?.id === 'pitwall') {
      tabs.forEach(t => t.classList.remove('active'));
      panels.forEach(p => p.classList.remove('active'));
      document.querySelector('[data-tab="capteurs"]').classList.add('active');
      document.getElementById('capteurs').classList.add('active');
    }
  }
});

// Exposer pour les autres modules
window.App = { tnow, currentMode: () => currentMode };
