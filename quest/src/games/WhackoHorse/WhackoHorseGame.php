<?php

declare(strict_types=1);

namespace megarabyte\quest\games\WhackoHorse;

use megarabyte\worldshandler\WorldHandler;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\Position;

class WhackoHorseGame
{
    private Player $player;

    function __construct(Player $player)
    {
        $this->player = $player;
        WorldHandler::duplicateWorld("worlds\whack-o-horse\preset", "worlds\whack-o-horse\\", "whack-o-horse-" . $player->getName());
        $player->teleport(new Position(0, 12, 0, WorldHandler::getWorldByString("whack-o-horse-" . $player->getName())));
        $this->setGameInventory($player);
    }

    public function setGameInventory(Player $player = null)
    {
        $player = $player ?? $this->player;
        $inv = $player->getInventory();
        $inv->clearAll();
        $inv->setItem(0, VanillaItems::WOODEN_SWORD());
    }
}
