</div><!-- /.wrap -->
</main>

<footer>Alpine Pitwall</footer>

<!-- ================================================================
     Scripts JS — ordre d'importance
================================================================ -->

<!-- Données SVG des tracés (statiques, côté client uniquement) -->
<script src="public/js/tracks.js"></script>

<!-- Logique principale : tabs, mode, horloge -->
<script src="public/js/app.js"></script>

<!-- Capteur température + météo -->
<script src="public/js/sensor.js"></script>

<!-- Contrôle LEDs (sync avec /api/leds) -->
<script src="public/js/leds.js"></script>

<!-- Mur des stands (pitwall) -->
<script src="public/js/pitwall.js"></script>

<!-- Classement / historique (interactions JS) -->
<script src="public/js/standings.js"></script>

<!-- Voiture 3D Three.js (module ES) -->
<script type="importmap">
{ "imports": {
  "three":          "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js",
  "three/addons/":  "https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"
}}
</script>
<script type="module" src="public/js/car3d.js"></script>

</body>
</html>
