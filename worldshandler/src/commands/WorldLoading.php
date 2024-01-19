<?php

declare(strict_types=1);

namespace worldshandler\commands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use worldshandler\WorldHandler;

class WorldLoading
{
    static function loadWorld(string $world): bool
    {
        return WorldHandler::loadWorld($world);
    }

    static function unloadWorld(string $world): ?bool
    {
        return WorldHandler::unloadWorld($world);
    }
}
