<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class RaceModel extends Model
{
    protected string $table = 'races';

    /** Retourne toutes les courses avec leur top 5 */
    public function getAllWithResults(): array
    {
        $races = $this->query("SELECT * FROM races ORDER BY round")->fetchAll();
        foreach ($races as &$race) {
            $race['top5'] = $this->query(
                "SELECT driver_name, team FROM race_results
                  WHERE race_id = ? ORDER BY position",
                [$race['id']]
            )->fetchAll();
        }
        unset($race);
        return $races;
    }

    public function getDriverStandings(): array
    {
        return $this->query(
            "SELECT * FROM driver_standings ORDER BY position"
        )->fetchAll();
    }

    public function getConstructorStandings(): array
    {
        return $this->query(
            "SELECT * FROM constructor_standings ORDER BY position"
        )->fetchAll();
    }
}
