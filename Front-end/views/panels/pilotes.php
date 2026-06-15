<?php
/**
 * @var array  $drivers  Liste des pilotes depuis la BDD
 * @var string $mode     'daily' | 'race'  (transmis via JS côté client)
 */
$teamColors = [
    'Mercedes'     => '#00D7B6',
    'Ferrari'      => '#E8002D',
    'McLaren'      => '#FF8000',
    'Red Bull'     => '#3671C6',
    'Racing Bulls' => '#6692FF',
    'Alpine'       => '#FF5C9D',
    'Haas'         => '#B6BABD',
    'Williams'     => '#1868DB',
];
?>
<section class="panel" id="pilotes">
  <p class="eyebrow">BWT Alpine F1 Team &middot; 2026</p>
  <h1 class="ptitle">Nos <em>pilotes</em></h1>
  <p class="lead">
    Gasly et Colapinto sur l'A526 à moteur Mercedes.
    Les données live de télémétrie s'affichent en mode course.
  </p>

  <div class="grid g2" id="driverCards">
    <?php foreach ($drivers as $d): ?>
      <div class="card dcard"
           data-driver-id="<?= (int)$d['id'] ?>"
           data-fuel="<?= (int)$d['live_fuel'] ?>"
           data-brake="<?= (int)$d['live_brake'] ?>"
           data-ers="<?= (int)$d['live_ers'] ?>"
           data-engine="<?= htmlspecialchars($d['live_engine_status']) ?>">
        <span class="corner"></span>

        <!-- En-tête pilote -->
        <div class="dcard-top">
          <div class="dcard-info">
            <div class="no"><?= (int)$d['number'] ?></div>
            <h4>
              <img src="<?= htmlspecialchars($d['flag_url'] ?? '') ?>" alt="drapeau" loading="lazy">
              <?= htmlspecialchars($d['name']) ?>
            </h4>
            <div class="meta">
              <?= htmlspecialchars($d['car']) ?> &middot; Mercedes PU &middot; BWT Alpine
            </div>
          </div>
          <div class="dcard-photo">
            <img src="<?= htmlspecialchars($d['img_url'] ?? '') ?>"
                 alt="<?= htmlspecialchars($d['name']) ?>"
                 loading="lazy">
          </div>
        </div>

        <!-- Stats mode quotidien -->
        <div class="dcard-stats daily-stats">
          <div class="dcard-stat">
            <div class="stat-label">Points 2026</div>
            <div class="val"><?= (int)$d['points'] ?></div>
          </div>
          <div class="dcard-stat">
            <div class="stat-label">Championnat</div>
            <div class="val">P<?= (int)$d['championship_pos'] ?></div>
          </div>
          <div class="dcard-stat">
            <div class="stat-label">Nationalité</div>
            <div class="val" style="font-size:18px"><?= htmlspecialchars($d['nationality'] ?? '--') ?></div>
          </div>
          <div class="dcard-stat">
            <div class="stat-label">Âge</div>
            <div class="val"><?= (int)$d['age'] ?> <small>ans</small></div>
          </div>
        </div>

        <!-- Stats mode course (cachées par défaut) -->
        <div class="dcard-stats race-stats" style="display:none">
          <div class="dcard-stat">
            <div class="stat-label">Carburant</div>
            <div class="val"><?= (int)$d['live_fuel'] ?> <small>kg</small></div>
          </div>
          <div class="dcard-stat">
            <div class="stat-label">Temp. freins</div>
            <div class="val"><?= (int)$d['live_brake'] ?> <small>°C</small></div>
          </div>
          <div class="dcard-stat">
            <div class="stat-label">Charge ERS</div>
            <div class="val"><?= (int)$d['live_ers'] ?> <small>%</small></div>
          </div>
          <div class="dcard-stat">
            <div class="stat-label">Moteur</div>
            <div class="val" style="color:var(--green)"><?= htmlspecialchars($d['live_engine_status']) ?></div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
