<?php
/**
 * @var array $races  Courses avec top5
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
<section class="panel" id="historique">
  <p class="eyebrow">Saison 2026 &middot; résultats</p>
  <h1 class="ptitle">Courses <em>passées</em></h1>
  <p class="lead">Clique sur une course pour voir le top 5 détaillé.</p>

  <div class="toggle-detail" id="histToggle">
    <button data-h="full"   class="on">Complet</button>
    <button data-h="alpine">Alpine seul</button>
  </div>

  <div id="raceList">
    <?php foreach ($races as $r): ?>
      <div class="race-item">
        <div class="race-bar">
          <span class="rd">R<?= (int)$r['round'] ?></span>
          <span class="flag"><?= $r['flag_emoji'] ?></span>
          <div>
            <h4>GP <?= htmlspecialchars($r['gp_name']) ?></h4>
            <div class="sub"><?= htmlspecialchars($r['circuit_name']) ?> &middot; <?= htmlspecialchars($r['race_date']) ?></div>
          </div>
          <div class="win">
            <b><?= htmlspecialchars($r['winner_name']) ?></b><br>
            <span><?= htmlspecialchars($r['winner_team']) ?></span>
          </div>
          <span class="arr">&#8964;</span>
        </div>

        <!-- Détail complet (top 5) -->
        <div class="race-detail hist-full">
          <div class="race-detail-inner">
            <table>
              <thead>
                <tr><th>Pos</th><th>Pilote</th><th>Écurie</th></tr>
              </thead>
              <tbody>
                <?php foreach ($r['top5'] as $i => $res): ?>
                  <tr>
                    <td class="pos"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($res['driver_name']) ?></td>
                    <td>
                      <span class="teamdot" style="background:<?= $teamColors[$res['team']] ?? '#888' ?>"></span>
                      <?= htmlspecialchars($res['team']) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <p class="stat-sub" style="margin-top:12px">
              Pole : <?= htmlspecialchars($r['pole_position']) ?>
              &middot; Meilleur tour : <?= htmlspecialchars($r['fastest_lap']) ?>
              &middot; Alpine : <?= htmlspecialchars($r['alpine_result']) ?>
            </p>
          </div>
        </div>

        <!-- Détail Alpine uniquement (caché par défaut) -->
        <div class="race-detail hist-alpine" style="display:none">
          <div class="race-detail-inner">
            <div class="banner">
              <i>🏎️</i>
              <div>
                <b>Résultat Alpine — GP <?= htmlspecialchars($r['gp_name']) ?></b>
                <p><?= htmlspecialchars($r['alpine_result']) ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
