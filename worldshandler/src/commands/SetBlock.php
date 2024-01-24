<?php

declare(strict_types=1);

namespace worldshandler\commands;

use pocketmine\block\VanillaBlocks;
use pocketmine\world\Position;
use RecursiveIteratorIterator;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use worldshandler\WorldHandler;

class SetBlock
{

    private int $x;
    private int $y;
    private int $z;
    private Position $pos;
    public function __construct(int $x, int $y, int $z, $player)
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
