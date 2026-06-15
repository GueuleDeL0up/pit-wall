<?php
/** @var array $circuits  Circuits groupés [groupe => [circuit, ...]] */
?>
<section class="panel active" id="capteurs">
  <p class="eyebrow">Capteur de température &middot; circuits F1</p>
  <h1 class="ptitle">Station <em>températures</em></h1>
  <p class="lead">
    En haut, le tracé et la météo de référence du circuit choisi.
    En bas, lecture en temps réel de ton capteur physique.
  </p>

  <!-- Sélecteur circuit -->
  <div class="section-head">
    <h3><span class="ico">&#9678;</span>Circuit sélectionné</h3>
    <select id="circSelect">
      <?php foreach ($circuits as $groupName => $list): ?>
        <optgroup label="<?= htmlspecialchars($groupName) ?>">
          <?php foreach ($list as $c): ?>
            <option
              value="<?= htmlspecialchars($c['track_id']) ?>"
              data-lat="<?= $c['latitude'] ?>"
              data-lon="<?= $c['longitude'] ?>"
              data-name="<?= htmlspecialchars($c['name']) ?>"
              data-loc="<?= htmlspecialchars($c['location']) ?>"
              <?= $c['track_id'] === 'mc-1929' ? 'selected' : '' ?>
            >
              <?= $c['flag_emoji'] . ' ' . htmlspecialchars($c['name']) . ' — ' . htmlspecialchars($c['location']) ?>
            </option>
          <?php endforeach; ?>
        </optgroup>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Carte circuit -->
  <div class="card track-card" id="trackCard">
    <div class="track-svg-wrap">
      <svg id="trackSvg" viewBox="0 0 200 200">
        <defs>
          <linearGradient id="trackGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%"   stop-color="#1E6FC4"/>
            <stop offset="100%" stop-color="#FF5C9D"/>
          </linearGradient>
        </defs>
        <path id="trackPath"  class="track-line" d=""></path>
        <circle id="trackStart" class="track-start" r="4" cx="0" cy="0"></circle>
      </svg>
    </div>
    <div class="track-info">
      <h3 id="tiName">--</h3>
      <p class="loc" id="tiLoc">--</p>
      <div class="track-stats">
        <div class="track-stat"><div class="k">Température air</div><div class="v" id="wxTemp">--</div></div>
        <div class="track-stat"><div class="k">Humidité</div><div class="v" id="wxHum">--</div></div>
        <div class="track-stat"><div class="k">Ressenti</div><div class="v" id="wxFeel">--</div></div>
        <div class="track-stat"><div class="k">Ciel</div><div class="v" id="wxSky" style="font-size:15px;padding-top:3px">--</div></div>
      </div>
      <p class="stat-sub" id="circLabel" style="margin-top:14px">Chargement...</p>
    </div>
  </div>

  <!-- Lecture capteur -->
  <div class="card sensor-card" style="margin-top:8px">
    <span class="corner"></span>
    <div class="section-head" style="width:100%">
      <h3 style="margin:0"><span class="ico">&#9719;</span>Lecture du capteur</h3>
      <span class="chip idle" id="sensorStatus">en attente</span>
    </div>
    <div class="sensor-big idle" id="sensorTemp">--<span style="font-size:.4em;color:var(--grey)">&deg;</span></div>
    <div class="sensor-unit">température en degrés celsius</div>
    <div class="sensor-meta">
      <div><span>Min</span><b id="sMin">--</b></div>
      <div><span>Moy</span><b id="sAvg">--</b></div>
      <div><span>Max</span><b id="sMax">--</b></div>
      <div><span>Lectures</span><b id="sCount">0</b></div>
    </div>
    <div class="chart-wrap">
      <svg id="chart" viewBox="0 0 400 100" preserveAspectRatio="none">
        <defs>
          <linearGradient id="chartGrad" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%"   stop-color="#1E6FC4"/>
            <stop offset="100%" stop-color="#FF5C9D"/>
          </linearGradient>
          <linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%"   stop-color="#FF5C9D" stop-opacity=".35"/>
            <stop offset="100%" stop-color="#FF5C9D" stop-opacity="0"/>
          </linearGradient>
        </defs>
        <line class="chart-grid" x1="0" y1="25"  x2="400" y2="25"/>
        <line class="chart-grid" x1="0" y1="50"  x2="400" y2="50"/>
        <line class="chart-grid" x1="0" y1="75"  x2="400" y2="75"/>
        <path    id="chartFillPath" class="chart-fill" d=""></path>
        <polyline id="chartLine"   class="chart-line" points=""></polyline>
      </svg>
    </div>
    <p class="chart-empty" id="chartEmpty">Aucune lecture pour le moment</p>
    <div class="btn-row" style="justify-content:center">
      <button class="btn solid" id="demoBtn">Démarrer le mode démo</button>
      <button class="btn"       id="clearBtn">Effacer l'historique</button>
    </div>
    <p class="stream-info">
      Pour brancher le vrai capteur, appelle
      <code>window.pushSensorReading(valeur)</code> depuis ton script.
    </p>
  </div>
</section>
