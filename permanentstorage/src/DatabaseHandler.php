<?php

declare(strict_types=1);

namespace megarabyte\permanentstorage;

use pocketmine\player\Player;

abstract class DatabaseHandler
{
    const PLAYER_DATA_PATH = null;

    abstract public function __construct();

    /**
     * Validate Database to ensure all values are included. Only two parametres accepted.
     * 
     * @param Player $player Player you want to fetch the database of 
     * @param Database|null $database Database you want to validate. Leave blank or null to fetch the database of $player. 
     */
    // abstract public function validateDatabase(...$args);

    abstract public static function getDataArray(Player $player): array;

    abstract public static function getDatabase(Player $player): ?Database;
}
