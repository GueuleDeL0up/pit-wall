<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class CircuitModel extends Model
{
    protected string $table = 'circuits';

    /** Retourne tous les circuits groupés par group_name */
    public function getAllGrouped(): array
    {
        $rows    = $this->query("SELECT * FROM circuits ORDER BY group_name, id")->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['group_name']][] = $row;
        }
        return $grouped;
    }

    public function findByTrackId(string $trackId): array|false
    {
        return $this->query(
            "SELECT * FROM circuits WHERE track_id = ?",
            [$trackId]
        )->fetch();
    }
}
