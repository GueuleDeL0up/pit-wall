<?php
declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SensorModel.php';
require_once __DIR__ . '/../models/LedModel.php';

class ApiController extends Controller
{
    private SensorModel $sensor;
    private LedModel    $led;

    public function __construct()
    {
        $this->sensor = new SensorModel();
        $this->led    = new LedModel();
    }

    // ----------------------------------------------------------
    // GET  /api/sensor          → dernières 40 lectures
    // POST /api/sensor          → { value: float, circuit_id?: int }
    // DELETE /api/sensor        → vide l'historique
    // ----------------------------------------------------------
    public function sensor(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $rows  = $this->sensor->getRecent();
            $stats = $this->sensor->getStats();
            $this->json(['readings' => $rows, 'stats' => $stats]);
        }

        if ($method === 'POST') {
            $body  = $this->getJsonBody();
            $value = filter_var($body['value'] ?? null, FILTER_VALIDATE_FLOAT);
            if ($value === false) {
                $this->json(['error' => 'Valeur invalide'], 422);
            }
            $circuitId = isset($body['circuit_id']) ? (int)$body['circuit_id'] : null;
            $id        = $this->sensor->add($value, $circuitId);
            $stats     = $this->sensor->getStats();
            $this->json(['id' => $id, 'value' => $value, 'stats' => $stats]);
        }

        if ($method === 'DELETE') {
            $this->sensor->clearAll();
            $this->json(['ok' => true]);
        }

        $this->json(['error' => 'Méthode non supportée'], 405);
    }

    // ----------------------------------------------------------
    // GET  /api/leds                  → tous les états
    // POST /api/leds?action=toggle    → { id: int }
    // POST /api/leds?action=all       → { state: bool }
    // ----------------------------------------------------------
    public function leds(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';

        if ($method === 'GET') {
            $this->json($this->led->getAll());
        }

        if ($method === 'POST') {
            $body = $this->getJsonBody();

            if ($action === 'toggle') {
                $id = (int)($body['id'] ?? 0);
                if ($id < 1 || $id > 4) {
                    $this->json(['error' => 'ID LED invalide'], 422);
                }
                $updated = $this->led->toggle($id);
                $this->json($updated);
            }

            if ($action === 'all') {
                $state = (bool)($body['state'] ?? false);
                $this->led->setAll($state);
                $this->json(['ok' => true, 'state' => $state]);
            }
        }

        $this->json(['error' => 'Action non reconnue'], 400);
    }

    // ----------------------------------------------------------
    // GET /api/weather?lat=&lon=
    // Proxy vers Open-Meteo pour éviter les problèmes CORS
    // ----------------------------------------------------------
    public function weather(): void
    {
        $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
        $lon = filter_input(INPUT_GET, 'lon', FILTER_VALIDATE_FLOAT);

        if ($lat === false || $lat === null || $lon === false || $lon === null) {
            $this->json(['error' => 'Coordonnées invalides'], 422);
        }

        $url = sprintf(
            'https://api.open-meteo.com/v1/forecast?latitude=%s&longitude=%s'
            . '&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
            $lat, $lon
        );

        $ctx = stream_context_create(['http' => ['timeout' => 8, 'method' => 'GET']]);
        $raw = @file_get_contents($url, false, $ctx);

        if ($raw === false) {
            $this->json(['error' => 'API météo indisponible'], 503);
        }

        $data = json_decode($raw, true);
        if (!$data) {
            $this->json(['error' => 'Réponse météo invalide'], 502);
        }

        $this->json($data);
    }
}
