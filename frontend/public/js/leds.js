/* ================================================================
   leds.js — Signaux stand : contrôle individuel avec persistance BDD via /api/leds
================================================================ */

'use strict';

const ledGrid  = document.getElementById('ledGrid');
const ledCards = () => ledGrid.querySelectorAll('.led-card');

// ---- Mise à jour de l'affichage d'une card ----
function applyLedState(card, isOn) {
  card.classList.toggle('on', isOn);
  card.querySelector('.status').textContent = isOn ? 'ON' : 'OFF';
}

function updateSummary() {
  const total = ledCards().length;
  const on    = [...ledCards()].filter(c => c.classList.contains('on')).length;
  document.getElementById('ledSummary').textContent = `${on} / ${total} actifs`;
}

// ---- Clic sur un signal (toggle individuel) ----
ledGrid.addEventListener('click', async e => {
  const card = e.target.closest('.led-card');
  if (!card) return;

  const id  = parseInt(card.dataset.id);
  const res = await fetch(`/api/leds?action=toggle`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id }),
  });

  if (res.ok) {
    const data = await res.json();
    applyLedState(card, Boolean(data.is_on));
    updateSummary();
  }
});

// Résumé initial
updateSummary();
