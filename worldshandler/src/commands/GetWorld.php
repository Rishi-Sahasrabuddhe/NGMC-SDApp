<?php

declare(strict_types=1);

namespace megarabyte\worldshandler\commands;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

class GetWorld
{
    public const GWHELP = TextFormat::BOLD . "Usage: " . TextFormat::RESET . "'/getworld [player]'\n" .
        "Get the current world of a player on the server.";


    /**
     * Find the world an online player is currently in. Can only be run by players (NOT CONSOLE).
     * 
     * @param string $playerName Name of player in a string
     * @param Player $playerObject Object of the player. Either $playerName or $playerObject should be provided.
     * @return false Returns false if the command is incomplete (both $playerName and $playerObject are empty or provided).
     * @return World|null Returns a World object if the world is successfully found, or null if the player is not found.
     */
    static function getWorldByPlayer(string $playerName = null, Player $playerObject = null): false|World|null
    {

        $server = Server::getInstance();


        if ($playerName === null && $playerObject === null) {
            return false;
        }
        if ($playerName !== null && $playerObject !== null) {
            return false;
        }

        if ($playerName !== null) {
            $playerObject = $server->getPlayerExact($playerName);
        }

        if ($playerObject === null) return null;

        return $playerObject->getWorld();
    }
}
