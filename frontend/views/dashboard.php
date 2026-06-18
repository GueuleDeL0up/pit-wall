<?php
/**
 * @var array $leds      Tableau de LED depuis la BDD partagée (leds_g2c)
 * @var array $drivers   Timing Alpine simulé (voir DriverModel)
 * @var array $buzzerLog Dernières commandes buzzer envoyées (commande_buzzer_g2e, groupe E)
 */
?>
<section id="dashboard">
  <p class="eyebrow">BWT Alpine F1 Team</p>
  <h1 class="ptitle">Pit <em>Wall</em></h1>

  <!-- Session -->
  <div class="grid g2" style="margin-bottom:18px">
    <div class="card">
      <div class="session-flag">
        <span style="width:7px;height:7px;border-radius:50%;background:var(--green);display:inline-block"></span>
        SESSION EN COURS
      </div>
      <p class="lap">TOUR ACTUEL</p>
      <div class="biglap">
        <span id="lapNow">1</span><small style="font-size:.4em;color:var(--grey)"> / <span id="lapTotalDisplay">66</span></small>
      </div>
      <div class="progress"><i id="lapBar"></i></div>
      <div class="btn-row">
        <button class="btn solid" id="lapBtn">Tour suivant</button>
        <button class="btn"       id="lapReset">Réinitialiser</button>
      </div>
      <div class="lap-total">
        <label for="lapTotalInput">Nombre de tours du circuit</label>
        <input type="number" id="lapTotalInput" min="1" max="999" value="66">
      </div>
    </div>

    <div class="card">
      <div class="stat-label"><span class="ico">&#11044;</span>Feux pitlane</div>
      <div class="lights" id="lights">
        <span></span><span></span><span></span><span></span><span></span>
      </div>
      <p style="text-align:center;font-weight:800;font-size:20px" id="lightTxt">PITLANE FERMÉE</p>
      <p style="text-align:center;font-family:var(--cond);letter-spacing:1px;color:var(--grey);text-transform:uppercase;font-size:13px" id="lightSub">
        Limite 80 km/h
      </p>
      <div class="btn-row" style="justify-content:center">
        <button class="btn" id="pitBtn">Ouvrir / fermer</button>
      </div>
    </div>
  </div>

  <!-- Capteur -->
  <div class="card sensor-card" style="margin-bottom:18px">
    <div class="section-head" style="width:100%">
      <h3 style="margin:0"><span class="ico">&#9719;</span>Capteur piste</h3>
      <span class="chip idle" id="sensorStatus">en attente</span>
    </div>

    <div class="sensor-row">
      <div class="sensor-block">
        <div class="sensor-big idle" id="sensorTemp">--<span class="unit">&deg;</span></div>
        <div class="sensor-unit">température</div>
      </div>
      <div class="sensor-block">
        <div class="sensor-big hum idle" id="sensorHum">--<span class="unit">%</span></div>
        <div class="sensor-unit">humidité</div>
      </div>
    </div>

    <div class="chart-wrap">
      <svg id="chart" viewBox="0 0 400 100" preserveAspectRatio="none">
        <line class="chart-grid" x1="0" y1="25"  x2="400" y2="25"/>
        <line class="chart-grid" x1="0" y1="50"  x2="400" y2="50"/>
        <line class="chart-grid" x1="0" y1="75"  x2="400" y2="75"/>
        <path     id="chartFillPath" class="chart-fill" d=""></path>
        <polyline id="chartLineTemp" class="chart-line temp" points=""></polyline>
        <polyline id="chartLineHum"  class="chart-line hum"  points=""></polyline>
      </svg>
    </div>
    <p class="chart-empty" id="chartEmpty">Aucune lecture pour le moment</p>

    <div class="btn-row" style="justify-content:center">
      <button class="btn" id="clearBtn">Effacer l'historique</button>
    </div>
  </div>

  <!-- Communication stand : LEDs + Buzzer -->
  <div class="grid g2" style="margin-bottom:18px">
    <div class="card">
      <div class="section-head">
        <h3><span class="ico">&#9776;</span>Signaux stand</h3>
        <span class="chip" id="ledSummary">
          <?= count(array_filter($leds, fn($l) => $l['is_on'])) ?> / <?= count($leds) ?> actifs
        </span>
      </div>
      <div class="led-grid" id="ledGrid">
        <?php foreach ($leds as $led): ?>
          <div
            class="led-card<?= $led['is_on'] ? ' on' : '' ?>"
            data-id="<?= (int)$led['id'] ?>"
            style="color:<?= htmlspecialchars($led['color']) ?>"
          >
            <div class="led-bulb"></div>
            <h4><?= htmlspecialchars($led['label']) ?></h4>
            <p>LED <?= (int)$led['id'] ?></p>
            <div class="status"><?= $led['is_on'] ? 'ON' : 'OFF' ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <div class="section-head">
        <h3><span class="ico">&#128226;</span>Buzzer stand</h3>
        <span class="chip" id="buzzerStatus">prêt</span>
      </div>
      <div class="buzzer-grid" id="buzzerGrid">
        <?php foreach (BuzzerModel::META as $cmd => $meta): ?>
          <button class="buzzer-btn" data-cmd="<?= htmlspecialchars($cmd) ?>" style="--c:<?= htmlspecialchars($meta['color']) ?>">
            <?= htmlspecialchars($meta['label']) ?>
          </button>
        <?php endforeach; ?>
      </div>
      <div id="buzzerLog">
        <?php foreach ($buzzerLog as $c): ?>
          <div class="feed-row">
            <span class="num"><?= htmlspecialchars(BuzzerModel::META[$c['commande']]['label'] ?? $c['commande']) ?></span>
            <span class="pill <?= $c['statut'] === 'done' ? 'done' : 'pending' ?>"><?= htmlspecialchars($c['statut']) ?></span>
            <span class="t"><?= htmlspecialchars($c['created_at']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Timing -->
  <div class="card" style="margin-bottom:18px">
    <div class="section-head">
      <h3><span class="ico">&#9201;</span>Timing Alpine</h3>
      <span class="chip">simulé</span>
    </div>
    <table class="timing-table">
      <thead>
        <tr><th>Pos</th><th>Pilote</th><th>Pneu</th><th>Dernier tour</th><th class="num">Écart</th><th class="num">Arrêts</th></tr>
      </thead>
      <tbody>
        <?php foreach ($drivers as $d): ?>
          <tr>
            <td class="pos">P<?= (int)$d['position'] ?></td>
            <td><b>#<?= (int)$d['number'] ?></b> <?= htmlspecialchars($d['name']) ?></td>
            <td><?= htmlspecialchars($d['tyre']) ?></td>
            <td><?= htmlspecialchars($d['last_lap']) ?></td>
            <td class="num"><?= htmlspecialchars($d['gap']) ?></td>
            <td class="num"><?= (int)$d['pit_stops'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Monoplaces -->
  <div class="card" style="margin-bottom:18px">
    <div class="section-head">
      <h3><span class="ico">&#127950;</span>Monoplaces Alpine</h3>
      <span class="chip">simulé</span>
    </div>

    <div class="car-photo-stage">
      <div class="car-photo-placeholder">🏎️</div>
      <div class="car-photo-badge">A526</div>
    </div>

    <div class="grid g2">
      <?php foreach ($drivers as $d): ?>
        <div class="car-stat-card">
          <div class="car-stat-head">
            <span class="chassis"><?= htmlspecialchars($d['chassis']) ?></span>
            <span class="driver">#<?= (int)$d['number'] ?> <?= htmlspecialchars($d['name']) ?></span>
          </div>
          <div class="grid" style="grid-template-columns:repeat(2,1fr); gap:14px; margin-top:14px">
            <div>
              <div class="stat-label">Moteur</div>
              <div class="stat-val sm"><?= (int)$d['engine_temp'] ?><small>&deg;C</small></div>
            </div>
            <div>
              <div class="stat-label">Pneu</div>
              <div class="stat-val sm"><?= htmlspecialchars($d['tyre']) ?></div>
            </div>
            <div>
              <div class="stat-label">Freins</div>
              <div class="stat-val sm"><?= (int)$d['live_brake'] ?><small>&deg;C</small></div>
            </div>
            <div>
              <div class="stat-label">ERS</div>
              <div class="stat-val sm"><?= (int)$d['live_ers'] ?><small>%</small></div>
            </div>
            <div>
              <div class="stat-label">Carburant</div>
              <div class="stat-val sm"><?= (int)$d['live_fuel'] ?><small>kg</small></div>
            </div>
            <div>
              <div class="stat-label">Statut</div>
              <div class="stat-val sm" style="color:var(--green)"><?= htmlspecialchars($d['live_engine_status']) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Stratégie -->
  <div class="grid g2">
    <div class="card">
      <div class="section-head">
        <h3><span class="ico">&#8633;</span>Trafic stand</h3>
        <span class="chip">simulé</span>
      </div>
      <div id="feed"></div>
      <div class="btn-row">
        <button class="btn" id="inBtn">Entrée stand</button>
        <button class="btn" id="outBtn">Sortie stand</button>
      </div>
    </div>

    <div class="card">
      <div class="section-head">
        <h3><span class="ico">&#9675;</span>Composés Pirelli</h3>
        <span class="chip">disponibles</span>
      </div>
      <div id="tyreList"></div>
    </div>
  </div>
</section>
