<?php

declare(strict_types=1);

namespace megarabyte\quest\events;

use megarabyte\permanentstorage\Database;
use megarabyte\quest\QuestData;
use megarabyte\quest\QuestScoreboard;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Server;

class QuestListener implements Listener
{
    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $questData = new QuestData();
        $db = Database::getDatabaseFromPath(QuestData::PLAYER_DATA_PATH . $player->getName() . ".json");
        if ($db instanceof Database) {
            $questData->validateDatabase($player, $db);
        }
    }

}
