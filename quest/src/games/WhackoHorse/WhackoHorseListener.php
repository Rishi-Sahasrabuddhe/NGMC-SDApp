<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use megarabyte\lobby\inventories\GameTeleporterInventory;
use megarabyte\messageservice\HolographicText;
use megarabyte\quest\QuestData;
use megarabyte\worldshandler\WorldHandler;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
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
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\particle\Particle;
use pocketmine\world\Position;
use pocketmine\world\sound\PopSound;
use pocketmine\world\sound\Sound;

class LobbyListeners implements Listener
{
    public function onPlayerEntityInteraction(PlayerEntityInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $entity = $event->getEntity();
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
