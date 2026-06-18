<?php
declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../models/LedModel.php';
require_once __DIR__ . '/../models/BuzzerModel.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $data = [
            // Timing simulé (voir DriverModel) : contenu de démo type pit wall
            'drivers'   => (new DriverModel())->getAllDrivers(),
            // LEDs : table réelle de la BDD partagée (leds_g2c)
            'leds'      => (new LedModel())->getAll(),
            // Buzzer : table partagée du groupe E (commande_buzzer_g2e)
            'buzzerLog' => (new BuzzerModel())->getRecent(),
        ];

        $this->render('layout/header', $data);
        $this->render('dashboard',     $data);
        $this->render('layout/footer', $data);
    }
}
