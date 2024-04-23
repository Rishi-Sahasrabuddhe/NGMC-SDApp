<?php

declare(strict_types=1);

namespace megarabyte\lobby\lobbynpcs;

use megarabyte\eventsafeguard\PlayerEventSafeGuard as PESG;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;

class LobbyNPCListeners implements Listener
{
    public function onAttack(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            if (!PESG::playerHasListener($damager, self::class)) return;
            if ($entity instanceof Human) LobbyNPCManager::performNPCAction($entity, $damager);
            $event->cancel();
        }
    }

    public function onInteract(PlayerEntityInteractEvent $event): void
    {
        $entity = $event->getEntity();
        $player = $event->getPlayer();

        if (!PESG::playerHasListener($player, self::class)) return;
        if ($entity instanceof Human) LobbyNPCManager::performNPCAction($entity, $player);
    }
}
