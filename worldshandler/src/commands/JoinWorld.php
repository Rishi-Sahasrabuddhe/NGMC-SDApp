<?php

declare(strict_types=1);

namespace worldshandler\commands;

use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use worldshandler\WorldHandler;


class JoinWorld
{
    public const JWHELP = TextFormat::BOLD . "Usage: " . TextFormat::RESET . "'/joinworld <world-name>'\n";

    public static function joinWorld($world, $player): bool
    {
        if ($world instanceof World) {
            $world = $world->getFolderName(); // Transforms World to string
        }

        if (!is_string($world)) {
            return false; // Returns false if world is not a string
        }

        return WorldHandler::joinWorld($world, $player);
    }
}
