<?php

declare(strict_types=1);

namespace megarabyte\lobby\lobbynpcs;

use megarabyte\npc\HumanNPC;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\Server;

class LobbyNPCManager
{
    public function spawnLobbyNPCs(Player $player): void
    {
        (LeatherWorker::create())->spawnTo($player);
    }

    public static function performNPCAction(Human $npc, Player $player): void
    {
        switch ($npc->getNameTag()) {
            case 'Leather Worker':
                (new LeatherWorker($player, $npc))->openWorkerInventory($player);
                break;
        }
    }
}
