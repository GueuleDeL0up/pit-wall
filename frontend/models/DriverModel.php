<?php
declare(strict_types=1);

// Timing simulé des 2 Alpine (position, écart, dernier tour, pneu, arrêts) :
// pas de table dédiée, c'est du contenu de démo façon écran de pit wall.
class DriverModel
{
    private const DRIVERS = [
        [
            'number' => 10, 'name' => 'Pierre Gasly', 'chassis' => 'A526-01',
            'position' => 7, 'gap' => '+12.4s', 'last_lap' => '1:14.235',
            'tyre' => 'Medium', 'pit_stops' => 1,
            'engine_temp' => 104, 'live_fuel' => 48, 'live_brake' => 560, 'live_ers' => 90, 'live_engine_status' => 'OK',
        ],
        [
            'number' => 43, 'name' => 'Franco Colapinto', 'chassis' => 'A526-02',
            'position' => 11, 'gap' => '+24.1s', 'last_lap' => '1:15.012',
            'tyre' => 'Hard', 'pit_stops' => 2,
            'engine_temp' => 108, 'live_fuel' => 51, 'live_brake' => 545, 'live_ers' => 86, 'live_engine_status' => 'OK',
        ],
    ];

    public function getAllDrivers(): array
    {
        $rows = self::DRIVERS;
        usort($rows, fn($a, $b) => $a['position'] <=> $b['position']);
        return $rows;
    }
}
