<?php

declare(strict_types=1);

namespace megarabyte\worldshandler;

use Directory;
use FilesystemIterator;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\Filesystem;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\world\WorldCreationOptions;
use pocketmine\world\WorldException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
    static function isWorldLoaded(string $worldName): bool
    {
        $worldManager = self::getWorldManager();
        return $worldManager->isWorldLoaded($worldName);
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
        if ($world === null) return true;
        // if ($world->isLoaded() === true) return true;
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

    static function duplicateWorld(string $source, string $destination, string $newWorldName): string
    {
        if (!self::isWorldGenerated($destination . DIRECTORY_SEPARATOR . $newWorldName)) {
            self::unloadWorld($source);
            $worldsPath = Server::getInstance()->getDataPath() . 'worlds' . DIRECTORY_SEPARATOR;
            $sourcePath = $worldsPath . $source;
            $destinationPath = $worldsPath . $destination . DIRECTORY_SEPARATOR . $newWorldName;

            if (!is_dir(dirname($destinationPath))) {
                mkdir(dirname($destinationPath), 0777, true);
            }

            Filesystem::recursiveCopy($sourcePath, $destinationPath);
            $dirContents = scandir($sourcePath);
            foreach ($dirContents as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $itemSourcePath = $sourcePath . DIRECTORY_SEPARATOR . $item;
                $itemDestinationPath = $destinationPath . DIRECTORY_SEPARATOR . $item;

                if (is_file($itemSourcePath)) {
                    copy($itemSourcePath, $itemDestinationPath);
                }
            }
        }

        self::loadWorld($destination . DIRECTORY_SEPARATOR . $newWorldName);
        if (!self::isWorldLoaded($destination . DIRECTORY_SEPARATOR . $newWorldName)) throw new WorldException("World " . $destination . DIRECTORY_SEPARATOR . $newWorldName . "not loaded!");
        else (self::getWorldByString($destination . DIRECTORY_SEPARATOR . $newWorldName))->setDisplayName($newWorldName);

        return $destination . DIRECTORY_SEPARATOR . $newWorldName;
    }
}
