<?php

declare(strict_types=1);

namespace worldshandler;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

# Commands
use worldshandler\commands\GeneralCommandChecker as GeneralCC;
use worldshandler\commands\NewWorld as NW;

class Main extends PluginBase
{

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case 'newworld':
                if (!GeneralCC::checkIfHasPermission($sender, $command)) { // Checks if sender has permission to run the command
                    $sender->sendMessage(GeneralCC::permissionValidationMessage($command)); // Sends sender error message if they do not have the required permissions
                    return true;
                }

                $worldType = isset($args[0]) ? $args[0] : 'help'; // Sets world type

                if ($worldType === 'help') {
                    $sender->sendMessage(NW::NWHELP); // Sends help message
                    return true;
                }
                if (!isset($args[1])) { // Checks if world name is not included
                    $sender->sendMessage("Error: Please include a world name.");
                    return false;
                }
                $worldName = NW::cleanWorldName($args[1]); // Removes illegal characters in world name

                switch ($worldType) {
                    case 'void':
                        if (NW::createVoidWorld($worldName)) { // Returns true if void world successfully created.
                            $sender->sendMessage("Void world '$worldName' successfully created!");
                            return true;
                        } else {
                            if (WorldHandler::isWorldGenerated($worldName)) { // Returns true if world was already previously generated
                                $sender->sendMessage("World $worldName already exists!");
                                return true;
                            }
                            $sender->sendMessage("Error: World creation failed. Please try again later.");
                        }
                        break;
                    default:
                        $sender->sendMessage("Error: Unknown world type: " . TextFormat::ITALIC . $args[0] . TextFormat::RESET . "!");
                        break;
                }
                break;
            default:
                $sender->sendMessage("Error: Unknown command /" . $command->getName() . ". Please try again.");
                break;
        }
        return true;
    }
}
