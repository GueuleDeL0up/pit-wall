<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);

// Auto-chargement minimal
spl_autoload_register(function (string $class): void {
    $dirs = [
        BASE_PATH . '/controllers/',
        BASE_PATH . '/models/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/controllers/Controller.php';
require_once BASE_PATH . '/controllers/HomeController.php';
require_once BASE_PATH . '/controllers/ApiController.php';

// Résolution URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/';

// Supprime le sous-chemin si déployé dans un sous-dossier
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base)) ?: '/';
}

// Routeur
try {
    match (true) {
        $uri === '/' || $uri === '/index.php'
            => (new HomeController())->index(),

        $uri === '/api/sensor'
            => (new ApiController())->sensor(),

        $uri === '/api/leds'
            => (new ApiController())->leds(),

        $uri === '/api/weather'
            => (new ApiController())->weather(),

        default => (function () use ($uri): never {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => "Route introuvable : $uri"]);
            exit;
        })()
    };
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur base de données', 'detail' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur serveur', 'detail' => $e->getMessage()]);
}
