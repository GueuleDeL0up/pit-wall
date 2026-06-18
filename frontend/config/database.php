<?php
declare(strict_types=1);

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $localConfig = __DIR__ . '/local.php';
            if (!file_exists($localConfig)) {
                throw new RuntimeException(
                    "Configuration manquante : copiez frontend/config/local.php.example vers " .
                    "frontend/config/local.php et renseignez les identifiants (voir bdd partagée AlwaysData)."
                );
            }
            $config = require $localConfig;

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $config['db_host'],
                $config['db_name']
            );
            self::$instance = new PDO($dsn, $config['db_user'], $config['db_password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }
}
