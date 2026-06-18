/* ================================================================
   sensor.js — Lecture capteur (température/humidité)
================================================================ */

'use strict';

const MAX_READINGS = 40;
const POLL_INTERVAL_MS = 3000;

let readings = []; // [{ id, temperature, humidite }, ...]

function renderSensor() {
  const tempEl    = document.getElementById('sensorTemp');
  const humEl     = document.getElementById('sensorHum');
  const statusEl  = document.getElementById('sensorStatus');
  const chartTemp = document.getElementById('chartLineTemp');
  const chartHum  = document.getElementById('chartLineHum');
  const chartFill = document.getElementById('chartFillPath');
  const emptyMsg  = document.getElementById('chartEmpty');

  if (readings.length === 0) {
    tempEl.innerHTML = '--<span class="unit">°</span>';
    humEl.innerHTML  = '--<span class="unit">%</span>';
    tempEl.classList.add('idle');
    humEl.classList.add('idle');
    chartTemp.setAttribute('points', '');
    chartHum.setAttribute('points', '');
    chartFill.setAttribute('d', '');
    emptyMsg.style.display = 'block';
    return;
  }

  const last = readings[readings.length - 1];
  tempEl.innerHTML = `${last.temperature.toFixed(1)}<span class="unit">°</span>`;
  humEl.innerHTML  = `${last.humidite.toFixed(0)}<span class="unit">%</span>`;
  tempEl.classList.remove('idle');
  humEl.classList.remove('idle');
  statusEl.className = 'chip live';
  statusEl.textContent = 'en direct';

  emptyMsg.style.display = 'none';
  drawChart(readings);
}

function drawChart(rs) {
  const W = 400, H = 100, pp = 4;
  const temps = rs.map(r => r.temperature);
  const hums  = rs.map(r => r.humidite);
  const all   = temps.concat(hums);
  const mn    = Math.min(...all);
  const mx    = Math.max(...all);
  const range = Math.max(1, mx - mn);
  const pad   = range * 0.15;
  const lo = mn - pad, hi = mx + pad, r = hi - lo;
  const n  = rs.length;

  const toPoints = vs => vs.map((v, i) => {
    const x = n === 1 ? W / 2 : (i / (n - 1)) * W;
    const y = H - pp - ((v - lo) / r) * (H - pp * 2);
    return `${x.toFixed(1)},${y.toFixed(1)}`;
  });

  const tempPts = toPoints(temps);
  document.getElementById('chartLineTemp').setAttribute('points', tempPts.join(' '));
  document.getElementById('chartLineHum').setAttribute('points', toPoints(hums).join(' '));
  document.getElementById('chartFillPath').setAttribute(
    'd', `M 0,${H} L ${tempPts.join(' L ')} L ${W},${H} Z`
  );
}

// ---- Charger les lectures depuis la BDD ----
async function loadMesures() {
  try {
    const res  = await fetch('/api/sensor');
    const data = await res.json();
    if (Array.isArray(data.readings)) {
      readings = data.readings.slice(-MAX_READINGS);
      renderSensor();
    }
  } catch {
    // Backend/BDD indisponible : on garde le dernier état affiché
  }
}

// ---- Effacer historique ----
document.getElementById('clearBtn').addEventListener('click', async () => {
  await fetch('/api/sensor', { method: 'DELETE' }).catch(() => {});
  readings = [];
  renderSensor();
  document.getElementById('sensorStatus').className  = 'chip idle';
  document.getElementById('sensorStatus').textContent = 'en attente';
});

loadMesures();
setInterval(loadMesures, POLL_INTERVAL_MS);
