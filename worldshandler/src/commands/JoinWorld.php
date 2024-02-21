<?php

declare(strict_types=1);

namespace megarabyte\worldshandler\commands;

use pocketmine\utils\TextFormat;
use pocketmine\world\World;

use megarabyte\worldshandler\WorldHandler;


class JoinWorld
{
    public const JWHELP = TextFormat::BOLD . "Usage: " . TextFormat::RESET . "'/joinworld <world-name>'\n";

    public static function joinWorld($world, $player): bool
    {

        if ($world instanceof World) {
            $world = $world->getFolderName();
        }

        if (!is_string($world)) {
            return false;
        }

        return WorldHandler::joinWorld($world, $player);
    }
}
