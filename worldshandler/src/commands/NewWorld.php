<?php

declare(strict_types=1);

namespace worldshandler\commands;

use pocketmine\utils\TextFormat;
use worldshandler\WorldHandler;


class NewWorld
{
    public const NWHELP = "§lUsage:§r '/newworld <world-type> <world-name>'\n" .
        TextFormat::BOLD . "World Types:\n" . TextFormat::RESET .
        "- void\n" .
        TextFormat::BOLD . "World Names:\n" . TextFormat::RESET .
        "World names must be in lowercase and not include spaces. Dashes (-) excepted. No special characters allowed (!<>:'\/|?,)";

    public static function cleanWorldName(string $worldName): string
    {
        $worldName = strtolower($worldName);
        $worldName = str_replace(' ', '', $worldName);
        $worldName = preg_replace('/[^a-z0-9-]/', '', $worldName);
        return $worldName;
    }


    public static function createVoidWorld(string $worldName): bool
    {
        $options = WorldHandler::getVoidOptions();
        return WorldHandler::generateWorld($worldName, $options); // Returns true if world successfully generated, false if generated failed.
    }
}
