<?php

declare(strict_types=1);

namespace thebridge;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use thebridge\commands\CreateBridge;
use worldshandler\commands\GetWorld;
use worldshandler\WorldHandler;

class Main extends PluginBase
{
    function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case 'createbridge':
                if (isset($args[0])) {
                    $world = WorldHandler::getWorldByString($args[0]);
                } else {
                    if (!$sender instanceof Player) {
                        $sender->sendMessage(TextFormat::RED . "Only players can run this command!" . TextFormat::RESET . " To run as a console, specify the world name.");
                        return true;
                    } else {
                        $sender->teleport(new Vector3(0.5, 21, 0.5));
                    }
                    $world = GetWorld::getWorldByPlayer($sender->getName());
                }
                if ($world === null) {
                    $sender->sendMessage(TextFormat::RED . "World " . $args[0] . " does not exist." . TextFormat::RESET);
                    return false;
                }
                if (CreateBridge::createBridge($world) === true) {
                    $sender->sendMessage("Bridge successfully created in " . $world->getFolderName());
                } else {
                    $sender->sendMessage(TextFormat::RED . "Bridge creation failed! Check if this is a The Bridge compatible world.");
                }
                break;

            default:
                $sender->sendMessage("Error: Unknown command /" . $command->getName() . ". Please try again.");
                break;
        }
        return true;
    }
}
