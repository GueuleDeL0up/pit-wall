/* ================================================================
   leds.js — Contrôle LEDs avec persistance BDD via /api/leds
================================================================ */

'use strict';

const ledGrid   = document.getElementById('ledGrid');
const ledCards  = () => ledGrid.querySelectorAll('.led-card');
let chaseTimer  = null;

// ---- Mise à jour de l'affichage d'une card ----
function applyLedState(card, isOn) {
  card.classList.toggle('on', isOn);
  card.querySelector('.status').textContent = isOn ? 'ON' : 'OFF';
}

function updateSummary() {
  const total = ledCards().length;
  const on    = [...ledCards()].filter(c => c.classList.contains('on')).length;
  document.getElementById('ledSummary').textContent = `${on} / ${total} allumées`;
}

// ---- Clic sur une LED (toggle individuel) ----
ledGrid.addEventListener('click', async e => {
  const card = e.target.closest('.led-card');
  if (!card) return;

  stopChase();

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

// ---- Tout allumer ----
document.getElementById('allOn').addEventListener('click', async () => {
  stopChase();
  await fetch('/api/leds?action=all', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ state: true }),
  });
  ledCards().forEach(c => applyLedState(c, true));
  updateSummary();
});

// ---- Tout éteindre ----
document.getElementById('allOff').addEventListener('click', async () => {
  stopChase();
  await fetch('/api/leds?action=all', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ state: false }),
  });
  ledCards().forEach(c => applyLedState(c, false));
  updateSummary();
});

// ---- Inverser ----
document.getElementById('invert').addEventListener('click', async () => {
  stopChase();
  const cards = [...ledCards()];
  for (const card of cards) {
    const id  = parseInt(card.dataset.id);
    const res = await fetch('/api/leds?action=toggle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id }),
    });
    if (res.ok) {
      const data = await res.json();
      applyLedState(card, Boolean(data.is_on));
    }
  }
  updateSummary();
});

// ---- Séquence chase ----
function stopChase() {
  if (chaseTimer) {
    clearInterval(chaseTimer);
    chaseTimer = null;
  }
}

document.getElementById('chase').addEventListener('click', () => {
  if (chaseTimer) {
    stopChase();
    return;
  }

  const cards = [...ledCards()];
  let k = 0;

  chaseTimer = setInterval(async () => {
    for (let i = 0; i < cards.length; i++) {
      const id  = parseInt(cards[i].dataset.id);
      const on  = (i === k);
      applyLedState(cards[i], on);
      fetch('/api/leds?action=toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id }),
      }).catch(() => {});
    }
    k = (k + 1) % cards.length;
    updateSummary();
  }, 220);
});

// Résumé initial
updateSummary();
