<?php

declare(strict_types=1);

namespace worldshandler\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class GeneralCommandChecker
{
    static function checkInstanceOfPlayer($sender): bool
    {
        if ($sender instanceof Player) {
            return true;
        } else {
            return false;
        }
    }
    static function playerValidationMessage(): string
    {
        return "Error: You must be a player to run this command!";
    }

    static function checkIfHasPermission(CommandSender $sender, Command $command): bool
    {
        foreach ($command->getPermissions() as $permission) {
            if ($sender->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    static function permissionValidationMessage(): string
    {
        return "§cError: You don't have the required permission to run this command!";
    }
}
