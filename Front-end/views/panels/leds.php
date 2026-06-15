<?php
/** @var array $leds  Tableau de LED depuis la BDD */
?>
<section class="panel" id="leds">
  <p class="eyebrow">Actionneurs &middot; contrôle manuel</p>
  <h1 class="ptitle">Panneau <em>LEDs</em></h1>
  <p class="lead">
    Allume ou éteins individuellement chacune des 4 LEDs.
    L'état est persisté en base de données.
  </p>

  <!-- Contrôles généraux -->
  <div class="card" style="margin-bottom:18px">
    <div class="section-head">
      <h3><span class="ico">&#9776;</span>Contrôles généraux</h3>
      <span class="chip" id="ledSummary">
        <?= count(array_filter($leds, fn($l) => $l['is_on'])) ?> / <?= count($leds) ?> allumées
      </span>
    </div>
    <div class="btn-row" style="margin-top:0">
      <button class="btn solid" id="allOn">Tout allumer</button>
      <button class="btn"       id="allOff">Tout éteindre</button>
      <button class="btn"       id="invert">Inverser</button>
      <button class="btn"       id="chase">Séquence</button>
    </div>
  </div>

  <!-- Grille LEDs (rendue côté serveur, état initial depuis BDD) -->
  <div class="led-grid" id="ledGrid">
    <?php foreach ($leds as $led): ?>
      <div
        class="led-card<?= $led['is_on'] ? ' on' : '' ?>"
        data-id="<?= (int)$led['id'] ?>"
        style="color:<?= htmlspecialchars($led['color']) ?>"
      >
        <div class="led-bulb"></div>
        <h4>LED <?= (int)$led['id'] ?></h4>
        <p><?= htmlspecialchars($led['label']) ?></p>
        <div class="status"><?= $led['is_on'] ? 'ON' : 'OFF' ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
