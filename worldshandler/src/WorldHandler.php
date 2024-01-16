<?php

declare(strict_types=1);

namespace worldshandler;

use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\world\WorldCreationOptions;

class WorldHandler
{
    static function getFlatOptions(): WorldCreationOptions
    {
        $options = WorldCreationOptions::create();
        $options->setGeneratorOptions("2;1;0");
        return $options;
    }

    private static function getWorldManager(): WorldManager
    {
        $server = Server::getInstance();
        return $server->getWorldManager();
    }

    static function generateWorld($worldName, $options, $backgroundGeneration = true): bool
    {
        $worldManager = self::getWorldManager();
        return $worldManager->generateWorld($worldName, $options, $backgroundGeneration);
    }

    static function isWorldGenerated(string $worldName): bool
    {
        $worldManager = self::getWorldManager();
        return $worldManager->isWorldGenerated($worldName);
    }

    static function getWorldByString(string $worldName): World
    {
        $worldManager = self::getWorldManager();
        return $worldManager->getWorldByName($worldName);
    }
}
