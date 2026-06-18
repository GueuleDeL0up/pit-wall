/* ================================================================
   buzzer.js — Commandes buzzer vers le stand du groupe E
   (table partagée commande_buzzer_g2e, voir BuzzerModel.php)
================================================================ */

'use strict';

const LABELS = {
  BUZZER_PIT_STOP:   'Arrêt au stand',
  BUZZER_SAFETY_CAR: 'Voiture de sécurité',
  BUZZER_RELEASE:    'Relâcher',
  BUZZER_HOLD:       'Maintenir',
  BUZZER_EMERGENCY:  'Urgence',
  BUZZER_TEST:       'Test',
  BUZZER_OFF:        'Éteindre',
};

const buzzerGrid   = document.getElementById('buzzerGrid');
const buzzerLog    = document.getElementById('buzzerLog');
const buzzerStatus = document.getElementById('buzzerStatus');

function renderLog(commands) {
  buzzerLog.innerHTML = commands.map(c => `
    <div class="feed-row">
      <span class="num">${LABELS[c.commande] ?? c.commande}</span>
      <span class="pill ${c.statut === 'done' ? 'done' : 'pending'}">${c.statut}</span>
      <span class="t">${c.created_at}</span>
    </div>
  `).join('');
}

async function loadLog() {
  try {
    const res  = await fetch('/api/buzzer');
    const data = await res.json();
    if (Array.isArray(data.commands)) renderLog(data.commands);
  } catch {
    // BDD indisponible : on garde le dernier état affiché
  }
}

buzzerGrid.addEventListener('click', async e => {
  const btn = e.target.closest('.buzzer-btn');
  if (!btn) return;

  buzzerStatus.className  = 'chip';
  buzzerStatus.textContent = 'envoi…';

  try {
    const res = await fetch('/api/buzzer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ commande: btn.dataset.cmd }),
    });
    buzzerStatus.textContent = res.ok ? 'envoyé' : 'erreur';
    if (res.ok) loadLog();
  } catch {
    buzzerStatus.textContent = 'erreur';
  }

  setTimeout(() => { buzzerStatus.textContent = 'prêt'; }, 2000);
});

loadLog();
setInterval(loadLog, 5000);
