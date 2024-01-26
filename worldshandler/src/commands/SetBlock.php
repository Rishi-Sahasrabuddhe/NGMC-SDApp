<?php

declare(strict_types=1);

namespace megarabyte\worldshandler\commands;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SetBlock
{

    private int $x;
    private int $y;
    private int $z;
    private Position $pos;
    public function __construct(int $x, int $y, int $z, Player|CommandSender $player)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;

        $world = $player->getWorld();

        $this->pos = new Position($x, $y, $z, $world);

        $world->setBlock($this->pos, VanillaBlocks::BEDROCK());
    }

    function getX(): int
    {
        return $this->x;
    }
    function getY(): int
    {
        return $this->y;
    }
    function getZ(): int
    {
        return $this->z;
    }
    function getPos(): Position
    {
        return $this->pos;
    }
}
