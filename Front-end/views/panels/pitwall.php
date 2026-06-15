<section class="panel" id="pitwall">
  <p class="eyebrow">Grand Prix &middot; session live</p>
  <h1 class="ptitle">Mur des <em>stands</em></h1>
  <p class="lead">Disponible en mode course. Suivi de session, feux de pitlane et trafic du stand en temps réel.</p>

  <div class="grid g2" style="margin-bottom:18px">
    <!-- Tour actuel -->
    <div class="card">
      <span class="corner"></span>
      <div class="session-flag">
        <span style="width:7px;height:7px;border-radius:50%;background:var(--green);display:inline-block"></span>
        SESSION EN COURS
      </div>
      <p class="lap">TOUR ACTUEL</p>
      <div class="biglap">
        <span id="lapNow">1</span><small style="font-size:.4em;color:var(--grey)"> / 66</small>
      </div>
      <div class="progress"><i id="lapBar"></i></div>
      <div class="btn-row">
        <button class="btn solid" id="lapBtn">Tour suivant</button>
        <button class="btn"       id="lapReset">Réinitialiser</button>
      </div>
    </div>

    <!-- Feux pitlane -->
    <div class="card">
      <span class="corner"></span>
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

  <div class="grid g2">
    <!-- Trafic stand -->
    <div class="card">
      <div class="section-head">
        <h3><span class="ico">&#8633;</span>Trafic stand</h3>
        <span class="chip">temps réel</span>
      </div>
      <div id="feed"></div>
      <div class="btn-row">
        <button class="btn" id="inBtn">Entrée stand</button>
        <button class="btn" id="outBtn">Sortie stand</button>
      </div>
    </div>

    <!-- Composés Pirelli -->
    <div class="card">
      <div class="section-head">
        <h3><span class="ico">&#9675;</span>Composés Pirelli</h3>
        <span class="chip">disponibles</span>
      </div>
      <div id="tyreList"></div>
    </div>
  </div>
</section>
