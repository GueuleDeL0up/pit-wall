/* ================================================================
   standings.js — Classement pilotes/constructeurs + historique
================================================================ */

'use strict';

// ================================================================
// Classement — toggle Pilotes / Constructeurs
// ================================================================
const standDriversPanel = document.getElementById('standDriversPanel');
const standTeamsPanel   = document.getElementById('standTeamsPanel');

document.getElementById('standToggle').addEventListener('click', e => {
  const btn = e.target.closest('button');
  if (!btn) return;

  document.querySelectorAll('#standToggle button').forEach(b =>
    b.classList.toggle('on', b === btn)
  );

  const showDrivers = btn.dataset.s === 'drivers';
  standDriversPanel.style.display = showDrivers ? 'block' : 'none';
  standTeamsPanel.style.display   = showDrivers ? 'none'  : 'block';
});

// ================================================================
// Historique — toggle Complet / Alpine seul + accordéon
// ================================================================
let histView = 'full';

document.getElementById('histToggle').addEventListener('click', e => {
  const btn = e.target.closest('button');
  if (!btn) return;

  histView = btn.dataset.h;
  document.querySelectorAll('#histToggle button').forEach(b =>
    b.classList.toggle('on', b === btn)
  );

  // Afficher/masquer les bons détails dans chaque item ouvert
  document.querySelectorAll('.race-item').forEach(item => {
    item.querySelector('.hist-full').style.display   = histView === 'full'   ? '' : 'none';
    item.querySelector('.hist-alpine').style.display = histView === 'alpine' ? '' : 'none';
  });
});

// Accordéon courses
document.getElementById('raceList').addEventListener('click', e => {
  const bar = e.target.closest('.race-bar');
  if (!bar) return;
  bar.parentElement.classList.toggle('open');
});
