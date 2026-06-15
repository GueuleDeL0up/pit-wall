/* ================================================================
   sensor.js — Capteur température + météo circuit
================================================================ */

'use strict';

const WMO = {
  0:'Dégagé', 1:'Majoritairement clair', 2:'Partiellement nuageux', 3:'Couvert',
  45:'Brouillard', 48:'Brouillard givrant',
  51:'Bruine légère', 53:'Bruine modérée', 55:'Bruine forte',
  61:'Pluie légère',  63:'Pluie modérée',  65:'Forte pluie',
  71:'Neige légère',  73:'Neige modérée',  75:'Forte neige',
  80:'Averses légères', 81:'Averses modérées', 82:'Averses violentes',
  95:'Orage', 96:'Orage grêle', 99:'Orage grêle forte',
};

const MAX_READINGS = 40;
let readings    = [];
let refTemp     = null;
let demoTimer   = null;

// ---- Météo ----
async function fetchWeather(lat, lon, label) {
  document.getElementById('circLabel').textContent = 'Chargement météo…';
  try {
    const res  = await fetch(`/api/weather?lat=${lat}&lon=${lon}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    const c = data.current;
    document.getElementById('wxTemp').innerHTML = Math.round(c.temperature_2m)   + '°C';
    document.getElementById('wxHum').textContent = c.relative_humidity_2m        + '%';
    document.getElementById('wxFeel').innerHTML  = Math.round(c.apparent_temperature) + '°C';
    document.getElementById('wxSky').textContent = WMO[c.weather_code] || '—';
    document.getElementById('circLabel').textContent = `${label} — live Open-Meteo`;
    refTemp = Math.round(c.temperature_2m);
  } catch {
    document.getElementById('circLabel').textContent = 'Erreur réseau météo';
  }
}

// ---- Tracé circuit ----
function drawTrack(trackId, name, loc) {
  document.getElementById('tiName').textContent = name;
  document.getElementById('tiLoc').textContent  = loc;

  if (typeof TRACKS === 'undefined' || !TRACKS[trackId]) {
    document.getElementById('trackPath').setAttribute('d', '');
    document.getElementById('trackStart').setAttribute('r', '0');
    return;
  }

  const t    = TRACKS[trackId];
  const path = document.getElementById('trackPath');
  path.setAttribute('d', t.p);

  // Ré-animer le tracé
  const len = path.getTotalLength();
  path.style.setProperty('--len', len);
  path.style.animation = 'none';
  void path.getBBox();
  path.style.animation = '';

  document.getElementById('trackStart').setAttribute('cx', t.sx);
  document.getElementById('trackStart').setAttribute('cy', t.sy);
  document.getElementById('trackStart').setAttribute('r', '4');
}

// ---- Sélecteur circuit ----
const circSelect = document.getElementById('circSelect');

function onCircuitChange() {
  const opt = circSelect.selectedOptions[0];
  const lat  = parseFloat(opt.dataset.lat);
  const lon  = parseFloat(opt.dataset.lon);
  const name = opt.dataset.name;
  const loc  = opt.dataset.loc;
  const tid  = circSelect.value;

  drawTrack(tid, name, loc);
  fetchWeather(lat, lon, name);
}

circSelect.addEventListener('change', onCircuitChange);
onCircuitChange(); // Appel initial

// ---- Lectures capteur ----
function renderSensor() {
  const sensorEl  = document.getElementById('sensorTemp');
  const statusEl  = document.getElementById('sensorStatus');
  const chartLine = document.getElementById('chartLine');
  const chartFill = document.getElementById('chartFillPath');
  const emptyMsg  = document.getElementById('chartEmpty');

  if (readings.length === 0) {
    sensorEl.innerHTML = '--<span style="font-size:.4em;color:var(--grey)">°</span>';
    sensorEl.classList.add('idle');
    document.getElementById('sMin').textContent   = '--';
    document.getElementById('sAvg').textContent   = '--';
    document.getElementById('sMax').textContent   = '--';
    document.getElementById('sCount').textContent = '0';
    chartLine.setAttribute('points', '');
    chartFill.setAttribute('d', '');
    emptyMsg.style.display = 'block';
    return;
  }

  const last = readings[readings.length - 1];
  sensorEl.innerHTML = `${last.toFixed(1)}<span style="font-size:.4em;color:var(--grey)">°</span>`;
  sensorEl.classList.remove('idle');
  statusEl.className = 'chip live';
  statusEl.textContent = 'en direct';

  const mn  = Math.min(...readings);
  const mx  = Math.max(...readings);
  const avg = readings.reduce((a, b) => a + b, 0) / readings.length;

  document.getElementById('sMin').textContent   = mn.toFixed(1) + '°';
  document.getElementById('sMax').textContent   = mx.toFixed(1) + '°';
  document.getElementById('sAvg').textContent   = avg.toFixed(1) + '°';
  document.getElementById('sCount').textContent = readings.length;

  emptyMsg.style.display = 'none';
  drawChart(readings, mn, mx);
}

function drawChart(vs, mn, mx) {
  const W = 400, H = 100, pp = 4;
  const range = Math.max(1, mx - mn);
  const pad   = range * 0.15;
  const lo = mn - pad, hi = mx + pad, r = hi - lo;
  const n  = vs.length;

  const pts = vs.map((v, i) => {
    const x = n === 1 ? W / 2 : (i / (n - 1)) * W;
    const y = H - pp - ((v - lo) / r) * (H - pp * 2);
    return `${x.toFixed(1)},${y.toFixed(1)}`;
  });

  document.getElementById('chartLine').setAttribute('points', pts.join(' '));
  document.getElementById('chartFillPath').setAttribute(
    'd', `M 0,${H} L ${pts.join(' L ')} L ${W},${H} Z`
  );
}

// ---- API push ----
async function pushToApi(value) {
  const opt = circSelect.selectedOptions[0];
  await fetch('/api/sensor', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ value }),
  }).catch(() => {});
}

// ---- Fonction publique ----
window.pushSensorReading = function (value) {
  if (typeof value !== 'number' || isNaN(value)) return;
  readings.push(value);
  if (readings.length > MAX_READINGS) readings.shift();
  renderSensor();
  pushToApi(value);
};

// ---- Charger les lectures existantes depuis la BDD ----
fetch('/api/sensor')
  .then(r => r.json())
  .then(data => {
    if (Array.isArray(data.readings)) {
      readings = data.readings.map(r => parseFloat(r.value));
      renderSensor();
    }
  })
  .catch(() => {});

// ---- Effacer historique ----
document.getElementById('clearBtn').addEventListener('click', () => {
  readings = [];
  renderSensor();
  document.getElementById('sensorStatus').className  = 'chip idle';
  document.getElementById('sensorStatus').textContent = 'en attente';
  fetch('/api/sensor', { method: 'DELETE' }).catch(() => {});
});

// ---- Mode démo ----
document.getElementById('demoBtn').addEventListener('click', () => {
  if (demoTimer) {
    clearInterval(demoTimer);
    demoTimer = null;
    document.getElementById('demoBtn').textContent         = 'Démarrer le mode démo';
    document.getElementById('sensorStatus').className      = 'chip idle';
    document.getElementById('sensorStatus').textContent    = 'en attente';
    return;
  }

  document.getElementById('demoBtn').textContent         = 'Arrêter le mode démo';
  document.getElementById('sensorStatus').className      = 'chip live';
  document.getElementById('sensorStatus').textContent    = 'démo';

  let cur = refTemp !== null ? refTemp : 22;
  const tick = () => {
    cur += (Math.random() - 0.5) * 1.4;
    cur  = Math.max(-10, Math.min(50, cur));
    window.pushSensorReading(+cur.toFixed(1));
  };
  tick();
  demoTimer = setInterval(tick, 1500);
});

renderSensor();
