<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use megarabyte\lobby\inventories\GameTeleporterInventory;
use megarabyte\messageservice\HolographicText;
use megarabyte\quest\QuestData;
use megarabyte\worldshandler\WorldHandler;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerEntityInteractEvent,
    PlayerInteractEvent,
    PlayerJoinEvent
};
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\sound\PopSound;

class LobbyListeners implements Listener
{
    function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        LobbyConstants::sendPlayerToSpawn($player);
        $main = Main::getInstance();
        $main->setLobbyInventory($player);

        LobbyConstants::$infoHolographic = new HolographicText(
            "Welcome, " . $player->getName() . "!",
            LobbyConstants::processHolographicText(LobbyConstants::getWelcomeHolographicText($player), 80),
            new Position(-12, 14.5, 0.5, WorldHandler::getWorldByString("lobby")),
            [$player]
        );
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
    }

    public function playerInteractEvent(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $item = $event->getItem();
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $main = Main::getInstance();
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($item !== null && $item->getTypeId() === VanillaItems::LEATHER()->getTypeId()) {
                LobbyBooks::howToUseLeather($player)->openBook($player);
                if (QuestData::getDataFromPlayer($player)["questProgress"] === 1) {
                    QuestData::getDatabase($player)->edit("questProgress", 2);
                    $player->getWorld()->addSound($player->getPosition(), new PopSound(), [$player]);
                    $player->sendTip("Click on the Game Teleporter in Slot 5!");
                    $main->setLobbyInventory($player);
                }
            }
            if ($item !== null && $item->getTypeId() === VanillaItems::COMPASS()->getTypeId()) {
                new GameTeleporterInventory($player);
            }
        }

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK || $action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            $blockPos = $block->getPosition();
            $blockX = $blockPos->getX();
            $blockY = $blockPos->getY();
            $blockZ = $blockPos->getZ();
            if ($blockX === -13 && 13 <= $blockY && $blockY <= 16 && 5 >= $blockZ && $blockZ >= -5) {
                if (QuestData::getDataFromPlayer($player)["questProgress"] === 0) {
                    QuestData::getDatabase($player)->edit("questProgress", 1);
                    $player->getWorld()->addSound($player->getPosition(), new PopSound(), [$player]);
                    $player->sendTip("Click on the Quest Manager in Slot 2!");
                    $main->setLobbyInventory($player);
                }
            }
        }
    }

    public function onPlayerBlockBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();

        if ($player->getGamemode() !== GameMode::CREATIVE()) {
            $event->cancel();
        }
    }

    public function onInventoryUpdate(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        if ($player->getGamemode() !== GameMode::CREATIVE) $event->cancel();
    }
}
