<?php

declare(strict_types=1);

namespace worldshandler\commands;

use worldshandler\WorldHandler;

class NewWorld
{
    public const NWHELP = "§lUsage:§r '/newworld <world-type> <world-name>'\n" .
        "§lWorld Types:§r\n" .
        "- void\n" .
        "§lWorld Names:§r\n" .
        "World names must be in lowercase and not include spaces. Dashes (-) excepted. No special characters allowed (!<>:'\\/|?,)";

    public static function cleanWorldName(string $worldName): string
    {
        $worldName = strtolower($worldName);
        $worldName = str_replace(' ', '', $worldName);
        $worldName = preg_replace('/[^a-z0-9-]/', '', $worldName);
        return $worldName;
    }


    public static function createVoidWorld(string $worldName): bool
    {
        $options = WorldHandler::getFlatOptions();
        return WorldHandler::generateWorld($worldName, $options, false);
    }
}
