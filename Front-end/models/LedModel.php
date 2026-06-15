<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

class LedModel extends Model
{
    protected string $table = 'led_states';

    public function getAll(): array
    {
        return $this->query("SELECT * FROM led_states ORDER BY id")->fetchAll();
    }

    public function toggle(int $id): array|false
    {
        $this->query(
            "UPDATE led_states SET is_on = IF(is_on, 0, 1) WHERE id = ?",
            [$id]
        );
        return $this->query(
            "SELECT * FROM led_states WHERE id = ?",
            [$id]
        )->fetch();
    }

    public function setAll(bool $state): void
    {
        $this->query("UPDATE led_states SET is_on = ?", [$state ? 1 : 0]);
    }

    public function setState(int $id, bool $state): void
    {
        $this->query(
            "UPDATE led_states SET is_on = ? WHERE id = ?",
            [$state ? 1 : 0, $id]
        );
    }

    public function updateLabel(int $id, string $label): void
    {
        $this->query(
            "UPDATE led_states SET label = ? WHERE id = ?",
            [htmlspecialchars($label, ENT_QUOTES), $id]
        );
    }
}
