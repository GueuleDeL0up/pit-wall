<?php
declare(strict_types=1);

// Pont vers le backend Python (pont série STM32, voir backend/app/main.py) :
// relaie les commandes LED pour un allumage instantané. Sans ça, le site PHP
// n'écrirait qu'en BDD et il faudrait attendre le polling du backend (jusqu'à 1s de délai).
class Backend
{
    private const BASE_URL = 'http://127.0.0.1:8000';

    /** Retourne true si le backend a bien relayé la commande au STM32. */
    public static function setLed(int $id, bool $etat): bool
    {
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => json_encode(['etat' => $etat]),
                'timeout' => 1,
            ],
        ]);

        return @file_get_contents(self::BASE_URL . "/api/leds/{$id}", false, $ctx) !== false;
    }
}
