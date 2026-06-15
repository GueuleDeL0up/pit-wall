<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class DriverModel extends Model
{
    protected string $table = 'drivers';

    public function getAllDrivers(): array
    {
        return $this->query(
            "SELECT * FROM drivers ORDER BY championship_pos"
        )->fetchAll();
    }

    public function updateLiveData(int $id, int $fuel, int $brake, int $ers, string $engineStatus): bool
    {
        return $this->query(
            "UPDATE drivers SET live_fuel=?, live_brake=?, live_ers=?, live_engine_status=? WHERE id=?",
            [$fuel, $brake, $ers, $engineStatus, $id]
        )->rowCount() > 0;
    }

    public function updatePoints(int $id, int $points, int $pos): bool
    {
        return $this->query(
            "UPDATE drivers SET points=?, championship_pos=? WHERE id=?",
            [$points, $pos, $id]
        )->rowCount() > 0;
    }
}
