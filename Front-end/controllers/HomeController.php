<?php
declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/CircuitModel.php';
require_once __DIR__ . '/../models/DriverModel.php';
require_once __DIR__ . '/../models/RaceModel.php';
require_once __DIR__ . '/../models/LedModel.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $circuitModel = new CircuitModel();
        $driverModel  = new DriverModel();
        $raceModel    = new RaceModel();
        $ledModel     = new LedModel();

        $data = [
            'circuits'     => $circuitModel->getAllGrouped(),
            'drivers'      => $driverModel->getAllDrivers(),
            'races'        => $raceModel->getAllWithResults(),
            'standDrivers' => $raceModel->getDriverStandings(),
            'standTeams'   => $raceModel->getConstructorStandings(),
            'leds'         => $ledModel->getAll(),
        ];

        $this->render('layout/header', $data);
        $this->render('panels/capteurs',    $data);
        $this->render('panels/leds',        $data);
        $this->render('panels/pitwall',     $data);
        $this->render('panels/pilotes',     $data);
        $this->render('panels/voiture',     $data);
        $this->render('panels/classement',  $data);
        $this->render('panels/historique',  $data);
        $this->render('layout/footer',      $data);
    }
}
