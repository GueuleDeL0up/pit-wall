<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

abstract class Model
{
    protected PDO    $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function findAll(): array
    {
        return $this->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    public function findById(int $id): array|false
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        )->fetch();
    }
}
