<?php

declare(strict_types=1);

namespace megarabyte\worldshandler;

use Directory;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\Filesystem;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\world\WorldCreationOptions;

class WorldHandler
{
    static function getVoidOptions(): WorldCreationOptions
    {
        $options = WorldCreationOptions::create();
        $options->setGeneratorClass("pocketmine\world\generator\Flat");
        $options->setGeneratorOptions("2;0;1");
        $options->setSpawnPosition(new Vector3(0.5, 21, 0.5));
        return $options;
    }

    private static function getWorldManager(): WorldManager
    {
        $server = Server::getInstance();
        return $server->getWorldManager();
    }

    static function generateWorld(string $worldName, WorldCreationOptions $options, bool $backgroundGeneration = true): bool
    {
        $worldManager = self::getWorldManager();
        return $worldManager->generateWorld($worldName, $options, $backgroundGeneration);
    }

    static function isWorldGenerated(string $worldName): bool
    {
        $worldManager = self::getWorldManager();
        return $worldManager->isWorldGenerated($worldName);
    }

    static function getWorldByString(string $worldName): ?World
    {
        $worldManager = self::getWorldManager();
        return $worldManager->getWorldByName($worldName);
    }

    static function loadWorld(string $world): bool
    {
        $worldManager = self::getWorldManager();
        return $worldManager->loadWorld($world);
    }
    static function unloadWorld(string $world): ?bool
    {
        $worldManager = self::getWorldManager();
        $world = self::getWorldByString($world);
        if ($world->isLoaded() === true) return true;
        else return $worldManager->unloadWorld($world, true);
    }

    static function joinWorld(string $world, Player $player, ?Position $pos = null): bool
    {
        if (!self::loadWorld($world)) return false; // Returns false if world doesn't load.
        $worldtypeworld = self::getWorldByString($world);
        if ($pos === null) {
            $player->teleport($worldtypeworld->getSpawnLocation()); // teleports player to world
        } else {
            $player->teleport($pos); // teleports player to world
        }
        return true;
    }

    static function duplicateWorld(string $source, string $destination, string $newWorldName): void
    {
        self::unloadWorld($source);
        Server::getInstance()->getLogger()->info($source);
        $worldsPath = Server::getInstance()->getDataPath() . 'worlds' . DIRECTORY_SEPARATOR;
        $sourcePath = $worldsPath . $source;
        $destination = $worldsPath . $destination . DIRECTORY_SEPARATOR . $newWorldName;
        Filesystem::recursiveCopy($sourcePath, $destination);
        self::getWorldByString($newWorldName)->setDisplayName($newWorldName);
    }
}
