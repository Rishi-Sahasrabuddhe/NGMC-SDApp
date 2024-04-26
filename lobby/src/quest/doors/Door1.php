<?php

declare(strict_types=1);

namespace megarabyte\lobby\quest\doors;

use megarabyte\messageservice\Error;
use megarabyte\quest\QuestData;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\block\Block;
use pocketmine\network\mcpe\protocol\BatchPacket;

class Door1
{

    public static function checkDoorUnlocked(Player $player): bool
    {
        $db = QuestData::getDatabase($player);
        foreach ($db->get('doorsUnlocked') as $door) if ($door == self::class) return true;
        return false;
    }

    public static function teleportThroughDoor(Player $player, bool $doorUnlocked = false)
    {
        if (self::checkDoorUnlocked($player)) {
            $location = $player->getLocation();
            $motion = $player->getMotion();
            $player->teleport(new Vector3($location->getX(), $location->getY(), 15.5), $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
            $player->setMotion($motion);
        } else $player->sendActionBarMessage((new Error("You have not yet unlocked this door!"))->sendError()
            . " You need " . strval(10000 - QuestData::getDatabase($player)->get('points')) . " more points.");
    }
    public static function teleportBACKThroughDoor(Player $player, bool $doorUnlocked = false)
    {
        $location = $player->getLocation();
        $motion = $player->getMotion();
        $player->teleport(new Vector3($location->getX(), $location->getY(), 11.5), $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
        $player->setMotion($motion);
    }
}
