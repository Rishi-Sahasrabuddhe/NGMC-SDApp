<?php

declare(strict_types=1);

namespace megarabyte\quest\events;

use megarabyte\permanentstorage\Database;
use megarabyte\quest\QuestData;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class QuestListener implements Listener
{
    public function onJoin(PlayerJoinEvent $event)
    {

        $player = $event->getPlayer();
        $questData = new QuestData();
        if (Database::getDatabaseFromPath(QuestData::PLAYER_DATA_PATH . $player->getName() . ".json") instanceof Database) {
            $questData->validateDatabase($player);
        }
    }
}
