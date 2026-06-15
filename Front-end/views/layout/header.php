<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alpine F1 — Pitwall & Capteurs</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Archivo+Narrow:wght@500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header>
  <div class="wrap">
    <div class="topbar">
      <div class="brand">
        <div class="logo">A</div>
        <div>
          <h1>Alpine&nbsp;Pitwall</h1>
          <span id="brandSub">Mode quotidien &middot; capteur</span>
        </div>
      </div>
      <div class="hctrl">
        <div class="mode-switch" id="modeSwitch">
          <button data-mode="daily" class="on">Quotidien</button>
          <button data-mode="race">Course</button>
        </div>
        <div class="live">
          <span class="dot"></span>
          <span id="clock">--:--:--</span>
        </div>
      </div>
    </div>

    <nav class="tabs" id="tabs">
      <button class="tab active" data-tab="capteurs"><i>&#9670;</i>Températures</button>
      <button class="tab"        data-tab="leds"><i>&#9670;</i>LEDs</button>
      <button class="tab race-only" data-tab="pitwall"><i>&#9670;</i>Pitwall</button>
      <button class="tab"        data-tab="pilotes"><i>&#9670;</i>Pilotes</button>
      <button class="tab"        data-tab="voiture"><i>&#9670;</i>A526</button>
      <button class="tab"        data-tab="classement"><i>&#9670;</i>Championnat</button>
      <button class="tab"        data-tab="historique"><i>&#9670;</i>Historique</button>
    </nav>
  </div>
</header>

<main>
<div class="wrap">
