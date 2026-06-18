<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class BuzzerModel extends Model
{
    protected string $table = 'commande_buzzer_g2e';

    // Table partagée gérée par le groupe E (buzzer physique) : on y dépose des
    // commandes, identifiées par notre groupe_source, qu'ils traitent de leur côté.
    private const GROUPE_SOURCE = 'groupe_g2c';

    // Label + couleur d'affichage pour chaque commande (la BDD ne stocke que le code)
    public const META = [
        'BUZZER_PIT_STOP'   => ['label' => 'Arrêt au stand',      'color' => '#1E6FC4'],
        'BUZZER_SAFETY_CAR' => ['label' => 'Voiture de sécurité', 'color' => '#FFC247'],
        'BUZZER_RELEASE'    => ['label' => 'Relâcher',            'color' => '#36E0A6'],
        'BUZZER_HOLD'       => ['label' => 'Maintenir',           'color' => '#FFC247'],
        'BUZZER_EMERGENCY'  => ['label' => 'Urgence',             'color' => '#FF5757'],
        'BUZZER_TEST'       => ['label' => 'Test',                'color' => '#7C93AC'],
        'BUZZER_OFF'        => ['label' => 'Éteindre',            'color' => '#7C93AC'],
    ];

    public function send(string $commande): array|false
    {
        if (!array_key_exists($commande, self::META)) {
            return false;
        }

        $this->query(
            "INSERT INTO commande_buzzer_g2e (commande, groupe_source) VALUES (?, ?)",
            [$commande, self::GROUPE_SOURCE]
        );
        $id = (int) $this->db->lastInsertId();

        return $this->query(
            "SELECT id, commande, statut, created_at FROM commande_buzzer_g2e WHERE id = ?",
            [$id]
        )->fetch();
    }

    public function getRecent(int $limit = 10): array
    {
        return $this->query(
            "SELECT id, commande, statut, created_at FROM commande_buzzer_g2e
             WHERE groupe_source = ? ORDER BY id DESC LIMIT " . $limit,
            [self::GROUPE_SOURCE]
        )->fetchAll();
    }
}
