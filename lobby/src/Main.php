<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use megarabyte\commands\GeneralCommandChecker;
use megarabyte\messageservice\Error;
use megarabyte\writtenbooks\Book;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;


class Main extends PluginBase
{
    private static ?self $instance = null;
    public LobbyConstants $lobbyConstants;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new LobbyListeners(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SignInteractions(), $this);
        $this->lobbyConstants = new LobbyConstants();
        self::$instance = $this;
    }

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, $args): bool
    {
        switch ($command->getName()) {
            case 'hub':
                if (!GeneralCommandChecker::checkInstanceOfPlayer($sender)) {
                    $sender->sendMessage(Error::NOTPLAYER);
                    return true;
                }
                LobbyConstants::sendPlayerToSpawn($sender);
                break;

            default:
                $sender->sendMessage(Error::unknownCommandError($command)->sendError());
                break;
        }
        return true;
    }

    public function setPlayerInventory(Player $player)
    {
        $player->selectHotbarSlot(4);
        $inventory = $player->getInventory();
        $inventory->setItem(1, VanillaItems::LEATHER());
    }

    public function clearPlayerInventory(Player $player)
    {
        $inventory = $player->getInventory();
        for ($i = 0; $i < 36; $i++) {
            $inventory->setItem($i, VanillaItems::AIR());
        }
    }
}
