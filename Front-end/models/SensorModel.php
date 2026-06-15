<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class SensorModel extends Model
{
    protected string $table = 'sensor_readings';

    /** Dernières N lectures, ordre chronologique pour le graphique */
    public function getRecent(int $limit = 40): array
    {
        $rows = $this->query(
            "SELECT * FROM sensor_readings ORDER BY recorded_at DESC LIMIT ?",
            [$limit]
        )->fetchAll();
        return array_reverse($rows);
    }

    public function add(float $value, ?int $circuitId = null): int
    {
        $this->query(
            "INSERT INTO sensor_readings (value, circuit_id) VALUES (?, ?)",
            [$value, $circuitId]
        );
        return (int) $this->db->lastInsertId();
    }

    public function getStats(): array
    {
        return $this->query(
            "SELECT
               MIN(value)   AS min_val,
               MAX(value)   AS max_val,
               AVG(value)   AS avg_val,
               COUNT(*)     AS total
             FROM sensor_readings"
        )->fetch();
    }

    public function clearAll(): void
    {
        $this->query("DELETE FROM sensor_readings");
    }
}
