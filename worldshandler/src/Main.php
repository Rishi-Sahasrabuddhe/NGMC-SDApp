<?php

declare(strict_types=1);

namespace worldshandler;

use messageservice\Error;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

# Commands
use pocketmine\world\WorldCreationOptions;
use commands\GeneralCommandChecker as GeneralCC;
use worldshandler\commands\GetWorld;
use worldshandler\commands\NewWorld as NW;
use worldshandler\commands\JoinWorld as JW;
use worldshandler\commands\SetBlock;
use worldshandler\commands\WorldLoading;

class Main extends PluginBase
{

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case 'loadworld':
                if (!GeneralCC::checkIfHasPermission($sender, $command)) { // Checks if sender has permission to run the command
                    $sender->sendMessage(GeneralCC::permissionValidationMessage($command)); // Sends sender error message if they do not have the required permissions
                    return true;
                }
                if (WorldLoading::loadWorld($args[0])) {
                    $sender->sendMessage("World " . $args[0] . "successfully loaded.");
                    return true;
                } else {
                    $sender->sendMessage("World " . $args[0] . "could not load. Check the name and try again.");
                    return false;
                }
            case 'unloadworld':
                if (!GeneralCC::checkIfHasPermission($sender, $command)) { // Checks if sender has permission to run the command
                    $sender->sendMessage(GeneralCC::permissionValidationMessage($command)); // Sends sender error message if they do not have the required permissions
                    return true;
                }
                if (WorldLoading::unloadWorld($args[0])) {
                    $sender->sendMessage("World " . $args[0] . "successfully loaded.");
                    return true;
                } elseif (WorldLoading::unloadWorld($args[0]) === null) {
                    $sender->sendMessage("World " . $args[0] . "not found. Check the name and try again.");
                    return true;
                } else {
                    $sender->sendMessage("World " . $args[0] . "could not load.");
                }
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
            case 'joinworld':
                if (!GeneralCC::checkInstanceOfPlayer($sender)) {
                    $sender->sendMessage(Error::NOTPLAYER);
                    return true;
                }
                if (!GeneralCC::checkIfHasPermission($sender, $command)) { // Checks if sender has permission to run the command
                    $sender->sendMessage(GeneralCC::permissionValidationMessage($command)); // Sends sender error message if they do not have the required permissions
                    return true;
                }

                $worldName = isset($args[0]) ? strtolower($args[0]) : 'help';

                if ($worldName === 'help') {
                    $sender->sendMessage(JW::JWHELP);
                    return true;
                }

                if (JW::joinWorld($worldName, $sender)) {
                    $sender->sendMessage($sender->getName() . " has successfully joined $worldName");
                } else {
                    $sender->sendMessage($sender->getName() . " could not join $worldName");
                }
                break;
            case 'getworld':
                if (!GeneralCC::checkIfHasPermission($sender, $command)) { // Checks if sender has permission to run the command
                    $sender->sendMessage(GeneralCC::permissionValidationMessage($command)); // Sends sender error message if they do not have the required permissions
                    return true;
                }
                $player = (isset($args[0])) ? $args[0] : $sender->getName();
                $world = GetWorld::getWorldByPlayer($player);
                if ($world instanceof World) {
                    $worldName = $world->getFolderName();
                    $sender->sendMessage("$player is in $worldName.");
                    return true;
                }
                if ($world === null) {
                    $sender->sendMessage("$player not found or is not online");
                    return true;
                }
                $sender->sendMessage(Error::INTERNAL);


                break;
            case 'setBlock':
                if (!GeneralCC::checkIfHasPermission($sender, $command)) { // Checks if sender has permission to run the command
                    $sender->sendMessage(GeneralCC::permissionValidationMessage($command)); // Sends sender error message if they do not have the required permissions
                    return true;
                }
                $x = isset($args[0]) ? $args[0] : 0;
                $y = isset($args[1]) ? $args[1] : 10;
                $z = isset($args[2]) ? $args[2] : 0;
                $setBlock = new SetBlock($x, $y, $z, $sender);
                $sender->sendMessage("Placed placeholder block at " . $setBlock->getPos()->__toString());
            default:
                $sender->sendMessage(Error::unknownCommandError($command)->sendError());
                break;
        }
        return true;
    }
}
