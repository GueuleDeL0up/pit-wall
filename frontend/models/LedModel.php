<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../config/backend.php';

class LedModel extends Model
{
    protected string $table = 'leds_g2c';

    // Signaux pour le stand (couleur + signification) : pas de colonnes dédiées
    // dans leds_g2c (BDD partagée), c'est une convention côté affichage.
    private const META = [
        1 => ['color' => '#36E0A6', 'label' => 'Sortie autorisée'],
        2 => ['color' => '#FFC247', 'label' => 'Voiture en approche'],
        3 => ['color' => '#FF5757', 'label' => 'Ravitaillement'],
        4 => ['color' => '#1E6FC4', 'label' => 'Changement pneus'],
    ];

    public function getAll(): array
    {
        $rows = $this->query("SELECT id, etat, updated_at FROM leds_g2c ORDER BY id")->fetchAll();
        return array_map(fn(array $row) => self::decorate($row), $rows);
    }

    public function toggle(int $id): array|false
    {
        $row = $this->query("SELECT etat FROM leds_g2c WHERE id = ?", [$id])->fetch();
        if ($row === false) {
            return false;
        }

        $this->applyState($id, !((bool)$row['etat']));

        $row = $this->query("SELECT id, etat, updated_at FROM leds_g2c WHERE id = ?", [$id])->fetch();
        return $row ? self::decorate($row) : false;
    }

    public function setAll(bool $state): void
    {
        for ($id = 1; $id <= 4; $id++) {
            $this->applyState($id, $state);
        }
    }

    /**
     * Relaie l'état au backend Python (BDD + commande série, instantané, voir
     * Backend::setLed). S'il est injoignable, on retombe sur une écriture BDD
     * directe : la LED ne s'allumera pas physiquement, mais l'état reste cohérent.
     */
    private function applyState(int $id, bool $etat): void
    {
        if (!Backend::setLed($id, $etat)) {
            $this->query(
                "UPDATE leds_g2c SET etat = ?, updated_at = NOW() WHERE id = ?",
                [(int)$etat, $id]
            );
        }
    }

    private static function decorate(array $row): array
    {
        $meta = self::META[(int)$row['id']] ?? ['color' => '#999999', 'label' => 'LED'];
        return [
            'id'         => (int)$row['id'],
            'is_on'      => (bool)$row['etat'],
            'color'      => $meta['color'],
            'label'      => $meta['label'],
            'updated_at' => $row['updated_at'],
        ];
    }
}
