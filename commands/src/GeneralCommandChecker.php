<?php

declare(strict_types=1);

namespace commands;

use messageservice\Error;
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
        $message = Error::NOPERM . "\n" .
            TextFormat::RESET . "You require one of the following permissions to execute this command:\n";

        $permissions = $command->getPermissions();

        foreach ($permissions as $permission) {
            $message .= "- $permission\n";
        }

        return $message;
    }
}
