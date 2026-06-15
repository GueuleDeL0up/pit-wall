/* ================================================================
   pitwall.js — Tour, feux pitlane, trafic stand, pneus
================================================================ */

'use strict';

// ---- Tour actuel ----
let lap      = 1;
const TOTAL  = 66;

function renderLap() {
  document.getElementById('lapNow').textContent    = lap;
  document.getElementById('lapBar').style.width    = Math.round((lap / TOTAL) * 100) + '%';
}

document.getElementById('lapBtn').addEventListener('click', () => {
  if (lap < TOTAL) { lap++; renderLap(); }
});

document.getElementById('lapReset').addEventListener('click', () => {
  lap = 1; renderLap();
});

renderLap();

// ---- Feux pitlane ----
let pitOpen = false;

function renderLights() {
  const lightsEl = document.getElementById('lights');
  const txtEl    = document.getElementById('lightTxt');
  const subEl    = document.getElementById('lightSub');

  lightsEl.classList.toggle('go', pitOpen);
  lightsEl.querySelectorAll('span').forEach(s => s.classList.toggle('on', true));

  if (pitOpen) {
    txtEl.textContent = 'PITLANE OUVERTE';
    subEl.textContent = 'Sorties autorisées';
  } else {
    txtEl.textContent = 'PITLANE FERMÉE';
    subEl.textContent = 'Limite 80 km/h';
  }
}

document.getElementById('pitBtn').addEventListener('click', () => {
  pitOpen = !pitOpen;
  renderLights();
});

renderLights();

// ---- Trafic stand ----
const FDRIVERS = ['GAS','COL','VER','LEC','NOR','RUS','HAM','PIA','ALO','SAI'];
const rnd = arr => arr[Math.floor(Math.random() * arr.length)];

function logPitEvent(type) {
  const feed  = document.getElementById('feed');
  const isIn  = type === 'in';
  const row   = document.createElement('div');
  row.className = 'feed-row';
  row.innerHTML = `
    <span class="num">${rnd(FDRIVERS)}</span>
    <span class="pill ${isIn ? 'in' : 'out'}">${isIn ? '▼ Entrée' : '▲ Sortie'}</span>
    <span class="t">${window.App?.tnow() ?? '--:--:--'}</span>
  `;
  feed.insertBefore(row, feed.firstChild);
  while (feed.children.length > 6) feed.removeChild(feed.lastChild);

  // Sortie de stand → ouvrir pitlane brièvement
  if (!isIn && !pitOpen) {
    pitOpen = true;
    renderLights();
    clearTimeout(window._pitTimer);
    window._pitTimer = setTimeout(() => {
      pitOpen = false;
      renderLights();
    }, 3200);
  }
}

document.getElementById('inBtn').addEventListener('click',  () => logPitEvent('in'));
document.getElementById('outBtn').addEventListener('click', () => logPitEvent('out'));

// Événements initiaux
logPitEvent('out');
logPitEvent('in');

// ---- Composés Pirelli ----
const TYRES = [
  { c: 'S', name: 'Soft · C5',     sub: 'Sec · qualif',    col: 'var(--red)',        pct: '2 sets' },
  { c: 'M', name: 'Medium · C3',   sub: 'Sec · course',    col: 'var(--amber)',      pct: '3 sets' },
  { c: 'H', name: 'Hard · C1',     sub: 'Sec · longue',    col: '#aaa',              pct: '2 sets' },
  { c: 'I', name: 'Intermediate',  sub: 'Piste humide',     col: 'var(--green)',      pct: '4 sets' },
  { c: 'W', name: 'Wet',           sub: 'Forte pluie',      col: 'var(--blue-500)',   pct: '3 sets' },
];

document.getElementById('tyreList').innerHTML = TYRES.map(t => `
  <div class="tyre">
    <div class="tyre-ring" style="border:4px solid ${t.col}">${t.c}</div>
    <div><h4>${t.name}</h4><p>${t.sub}</p></div>
    <span class="pct">${t.pct}</span>
  </div>
`).join('');
