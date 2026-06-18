<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class SensorModel extends Model
{
    protected string $table = 'Mesure';

    // Groupe assigné dans la BDD partagée (voir backend/.env)
    private const GROUPE = 'g2c';

    /**
     * Dernières N lectures, ordre chronologique pour le graphique.
     * Chaque lecture DHT11 écrit 2 lignes consécutives (temperature puis humidite,
     * voir backend/app/serial_reader.py) : on les repaire ici par id.
     */
    public function getRecent(int $limit = 40): array
    {
        $rows = $this->query(
            "SELECT id, type, valeur FROM Mesure WHERE groupe = ? ORDER BY id DESC LIMIT " . ($limit * 2),
            [self::GROUPE]
        )->fetchAll();

        $mesures = [];
        $pendingTemp = null;
        foreach (array_reverse($rows) as $row) {
            if ($row['type'] === 'temperature') {
                $pendingTemp = $row;
            } elseif ($row['type'] === 'humidite' && $pendingTemp !== null) {
                $mesures[] = [
                    'id'          => (int)$row['id'],
                    'temperature' => (float)$pendingTemp['valeur'],
                    'humidite'    => (float)$row['valeur'],
                ];
                $pendingTemp = null;
            }
        }

        return array_slice($mesures, -$limit);
    }

    public function clearAll(): void
    {
        $this->query(
            "DELETE FROM Mesure WHERE groupe = ? AND type IN ('temperature', 'humidite')",
            [self::GROUPE]
        );
    }
}
