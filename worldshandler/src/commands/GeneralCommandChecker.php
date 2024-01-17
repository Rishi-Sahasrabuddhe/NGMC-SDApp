<?php

declare(strict_types=1);

namespace worldshandler\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class GeneralCommandChecker
{
    static function checkInstanceOfPlayer($sender): bool
    {
        return $sender instanceof Player;
    }
    static function playerValidationMessage(): string
    {
        return "Error: You must be a player to run this command!";
    }

    static function checkIfHasPermission(CommandSender $sender, Command $command): bool
    {
        foreach ($command->getPermissions() as $permission) {
            if ($sender->hasPermission($permission)) { // Returns true if player has required permission
                return true;
            }
        }
        return false;
    }

    static function permissionValidationMessage(Command $command): string
    {
        $message = TextFormat::RED . "Error: You don't have the required permission to run this command!\n" .
            TextFormat::RESET . "You require one of the following permissions to execute this command:\n";

        $permissions = $command->getPermissions();

        foreach ($permissions as $permission) {
            $message .= "- $permission\n";
        }

        return $message;
    }
}
