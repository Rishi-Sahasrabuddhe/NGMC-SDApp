<?php

declare(strict_types=1);

namespace megarabyte\worldshandler;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
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

        return $worldManager->unloadWorld(self::getWorldByString($world));
    }

    static function joinWorld(string $world, Player $player, ?Position $pos = null): bool
    {
        if (!self::loadWorld($world)) {
            return false; // Returns false if world doesn't load.
        }
        $worldtypeworld = self::getWorldByString($world);
        if ($pos === null) {
            $player->teleport($worldtypeworld->getSpawnLocation()); // teleports player to world
        } else {
            $player->teleport($pos); // teleports player to world
        }
        return true;
    }

    static function duplicateWorld(string $sourceWorldPath, string $destinationPath, string $newWorldName): void
    {
        if (is_dir($sourceWorldPath)) {
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $dirContents = scandir($sourceWorldPath);
            foreach ($dirContents as $file) {
                if ($file != "." && $file != "..") {
                    $sourcePath = $sourceWorldPath . DIRECTORY_SEPARATOR . $file;
                    $newDirectory = $newWorldName . DIRECTORY_SEPARATOR . $file;
                    $destPath = $destinationPath . DIRECTORY_SEPARATOR . $newDirectory;
                    if (is_dir($sourcePath)) {
                        WorldHandler::duplicateWorld($sourcePath, $destPath, $newWorldName);
                    } else {
                        copy($sourcePath, $destPath);
                    }
                }
            }
        }

        self::getWorldByString($newWorldName)->setDisplayName($newWorldName);
    }
}
