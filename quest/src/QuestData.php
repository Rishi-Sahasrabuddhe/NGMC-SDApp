<?php

declare(strict_types=1);

namespace megarabyte\quest;

use megarabyte\permanentstorage\Database;
use pocketmine\player\Player;

class QuestData
{
    private array $defaultValues = [];
    const PLAYER_DATA_PATH = "plugins/quest/src/data/players/";

    public function __construct()
    {
        $this->defaultValues = [
            "name" => "",
            "questProgress" => 0,
            "chapter" => 1,
            "points" => 0,
            "leather" => 0,
            "doorsUnlocked" => [],
            "league" => 0,
            "inGame" => false
        ];
    }

    public function validateDatabase(Player $player, Database $database = null)
    {
        $db = $database ?? Database::getDatabaseFromPath(self::PLAYER_DATA_PATH . $player->getName() . ".json");
        $defaultValues = $this->defaultValues;
        $existingData = $db->read();
        if (empty($existingData)) {
            $existingData = $defaultValues;
        } else {
            // Loop through default values and add them if not present in existing data
            foreach ($defaultValues as $key => $defaultValue) {
                if (!isset($existingData[$key])) {
                    $existingData[$key] = $defaultValue;
                }
            }
        }
        $existingData["name"] = $player->getName();
        $db->write($existingData);
    }


    public static function getDataFromPlayer(Player $player): array
    {
        return Database::getDatabaseFromPath(self::PLAYER_DATA_PATH . $player->getName())->read();
    }

    public static function getDatabase(Player $player): ?Database
    {
        return Database::getDatabaseFromPath(self::PLAYER_DATA_PATH . $player->getName() . ".json");
    }

    public static function getGameStatus(Player $player): bool
    {
        return (self::getDataFromPlayer($player))["inGame"];
    }
}
