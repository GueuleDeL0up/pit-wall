<?php
/**
 * @var array $standDrivers  Classement pilotes
 * @var array $standTeams    Classement constructeurs
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
<section class="panel" id="classement">
  <p class="eyebrow">Championnat du monde FIA 2026</p>
  <h1 class="ptitle">Le <em>classement</em></h1>
  <p class="lead">Après 6 manches. Les pilotes et l'écurie Alpine sont mis en évidence.</p>

  <div class="toggle-detail" id="standToggle">
    <button data-s="drivers" class="on">Pilotes</button>
    <button data-s="teams">Constructeurs</button>
  </div>

  <!-- Classement pilotes -->
  <div class="card" style="padding:8px 16px" id="standDriversPanel">
    <table>
      <thead>
        <tr>
          <th>Pos</th>
          <th>Pilote</th>
          <th>Écurie</th>
          <th class="num">Points</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($standDrivers as $row): ?>
          <?php $isAlpine = ($row['team'] === 'Alpine'); ?>
          <tr class="<?= $isAlpine ? 'alp' : '' ?>">
            <td class="pos"><?= (int)$row['position'] ?></td>
            <td style="font-weight:<?= $isAlpine ? 700 : 500 ?>">
              <?= $row['nationality_flag'] ?> <?= htmlspecialchars($row['driver_name']) ?>
            </td>
            <td>
              <span class="teamdot" style="background:<?= $teamColors[$row['team']] ?? '#888' ?>"></span>
              <?= htmlspecialchars($row['team']) ?>
            </td>
            <td class="num" style="font-weight:700"><?= (int)$row['points'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Classement constructeurs (caché par défaut) -->
  <div class="card" style="padding:8px 16px;display:none" id="standTeamsPanel">
    <table>
      <thead>
        <tr>
          <th>Pos</th>
          <th>Écurie</th>
          <th class="num">Points</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($standTeams as $row): ?>
          <?php $isAlpine = ($row['team'] === 'Alpine'); ?>
          <tr class="<?= $isAlpine ? 'alp' : '' ?>">
            <td class="pos"><?= (int)$row['position'] ?></td>
            <td style="font-weight:<?= $isAlpine ? 700 : 500 ?>">
              <span class="teamdot" style="background:<?= $teamColors[$row['team']] ?? '#888' ?>"></span>
              <?= htmlspecialchars($row['team']) ?>
            </td>
            <td class="num" style="font-weight:700"><?= (int)$row['points'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
