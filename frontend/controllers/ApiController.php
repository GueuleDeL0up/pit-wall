<?php
declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SensorModel.php';
require_once __DIR__ . '/../models/LedModel.php';
require_once __DIR__ . '/../models/BuzzerModel.php';

class ApiController extends Controller
{
    private SensorModel $sensor;
    private LedModel    $led;
    private BuzzerModel $buzzer;

    public function __construct()
    {
        $this->sensor = new SensorModel();
        $this->led    = new LedModel();
        $this->buzzer = new BuzzerModel();
    }

    // ----------------------------------------------------------
    // GET    /api/sensor  → dernières lectures (temp+humidité, capteur réel)
    // DELETE /api/sensor  → vide l'historique
    // ----------------------------------------------------------
    public function sensor(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $this->json(['readings' => $this->sensor->getRecent()]);
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
    // GET  /api/buzzer  → dernières commandes envoyées par notre groupe
    // POST /api/buzzer  → { commande: string } (voir BuzzerModel::COMMANDS)
    // ----------------------------------------------------------
    public function buzzer(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $this->json(['commands' => $this->buzzer->getRecent()]);
        }

        if ($method === 'POST') {
            $body     = $this->getJsonBody();
            $commande = (string)($body['commande'] ?? '');
            $result   = $this->buzzer->send($commande);
            if ($result === false) {
                $this->json(['error' => 'Commande invalide'], 422);
            }
            $this->json($result);
        }

        $this->json(['error' => 'Méthode non supportée'], 405);
    }
}
