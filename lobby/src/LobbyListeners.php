<?php

declare(strict_types=1);

namespace megarabyte\lobby;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerEntityInteractEvent,
    PlayerInteractEvent,
    PlayerJoinEvent
};
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;

class LobbyListeners implements Listener
{
    function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        LobbyConstants::sendPlayerToSpawn($player);
        $main = Main::getInstance();
        $main->lobbyConstants->infoHolographic->updateHolographic(Server::getInstance()->getWorldManager()->getWorldByName("lobby")->getPlayers());
        $main->clearPlayerInventory($player);
        $main->setPlayerInventory($player);
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player) {
            $event->cancel();
        }
    }

    public function onPlayerEntityInteraction(PlayerEntityInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $entity = $event->getEntity();
        if ($entity instanceof FloatingTextParticle) {
            $main = Main::getInstance();
            $title = $player->getName() . "'s Stats";
            $stats = "Leather: \n";
            $stats .= "Wins: \n";
            $stats .= "Losses: \n";
            $main->lobbyConstants->infoHolographic->updateHolographic([$player], $title, $stats);
        }
    }

    public function onPlayerClickHotbarItem(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $item = $event->getItem();
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $main = Main::getInstance();
        // if ($action === UseItemTransactionData::RIGHT_CLICK_AIR) {
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($item !== null && $item->getTypeId() === VanillaItems::LEATHER()->getTypeId()) {
                LobbyBooks::howToUseLeather($player)->openBook($player);
            }
        }

        if ($item->getTypeId() === VanillaItems::WRITTEN_BOOK()->getTypeId()) {
            $player->sendMessage("Opened " . $item->getTitle());
        }
    }

    public function onPlayerBlockBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();

        if ($player->getGamemode() !== GameMode::CREATIVE()) {
            $event->cancel();
        }
    }
}
