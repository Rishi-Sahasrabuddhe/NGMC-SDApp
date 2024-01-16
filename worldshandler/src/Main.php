<?php

declare(strict_types=1);

namespace worldshandler;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

# Commands
use worldshandler\commands\GeneralCommandChecker as GeneralCC;
use worldshandler\commands\NewWorld as NW;

class Main extends PluginBase
{

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case 'newworld':
                if (!GeneralCC::checkIfHasPermission($sender, $command)) {
                    $sender->sendMessage(GeneralCC::permissionValidationMessage());
                    return false;
                }

                $arg = isset($args[0]) ? $args[0] : 'help';

                if ($arg === 'help') {
                    $sender->sendMessage(NW::NWHELP);
                    return true;
                }
                if (!isset($args[1])) {
                    $sender->sendMessage("Error: Please include a world name.");
                    return false;
                }
                $worldName = NW::cleanWorldName($args[1]);

                switch ($arg) {
                    case 'void':
                        if (NW::createVoidWorld($worldName)) {
                            $sender->sendMessage("Void world '$worldName' successfully created!");
                            return true;
                        } else {
                            if (WorldHandler::isWorldGenerated($worldName)) {
                                $sender->sendMessage("World $worldName already exists!");
                                return true;
                            }
                            $sender->sendMessage("Error: World creation failed. Please try again later.");
                        }
                        break;
                    default:
                        $sender->sendMessage("Error: Unknown world type: §o$args[0]§r!");
                        break;
                }
                break;
        }
        return true;
    }
}
