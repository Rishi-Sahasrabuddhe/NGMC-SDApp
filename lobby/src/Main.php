<?php

declare(strict_types=1);


namespace megarabyte\lobby;

use megarabyte\commands\GeneralCommandChecker;
use megarabyte\eventsafeguard\PlayerEventSafeGuard as PESG;
use megarabyte\lobby\inventories\GameTeleporterInventory;
use megarabyte\lobby\inventories\LobbyInventory;
use megarabyte\lobby\lobbynpcs\LobbyNPCListeners;
use megarabyte\messageservice\Error;
use megarabyte\quest\QuestData;
use megarabyte\worldshandler\WorldHandler;

use muqsit\invmenu\InvMenuHandler;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase
{

    private static ?self $instance = null;
    public LobbyConstants $lobbyConstants;

    public function onEnable(): void
    {
        $this->getServer()->getWorldManager()->setDefaultWorld(WorldHandler::getWorldByString("lobby"));
        $this->getServer()->getPluginManager()->registerEvents(new LobbyListeners(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SignInteractions(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new LobbyNPCListeners(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new inventories\GameTeleporterInventory(null), $this);
        $this->lobbyConstants = new LobbyConstants();
        self::$instance = $this;

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

    public function onDisable(): void
    {
        foreach (($this->getServer()->getOnlinePlayers()) as $player) {
            $player->kick('', null, 'Server shutdown.');
            QuestData::getDatabase($player)->edit('inGame', false);
        }
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

            case 'listeners':
                foreach ($sender->activeListeners as $listener) {
                    $sender->sendMessage($listener);
                }
                break;

            default:
                $sender->sendMessage(Error::unknownCommandError($command)->sendError());
                break;
        }
        return true;
    }

    public static function configureLobby(Player $player)
    {
        \megarabyte\quest\Main::checkPlayerProgress($player);
        PESG::config($player);
        PESG::addListener($player, self::class);
        PESG::addListener($player, GameTeleporterInventory::class);

        new LobbyInventory($player, 4);
    }
}
