<?php

declare(strict_types=1);

namespace megarabyte\worldshandler\commands;

use megarabyte\worldshandler\WorldHandler;

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
