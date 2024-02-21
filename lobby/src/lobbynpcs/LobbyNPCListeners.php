<?php

declare(strict_types=1);

namespace megarabyte\lobby\lobbynpcs;

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
        $event->cancel();
        $entity = $event->getEntity();
        if ($event->getDamager() instanceof Player)
            if ($entity instanceof Human) LobbyNPCManager::performNPCAction($entity, $event->getDamager());
    }

    public function onInteract(PlayerEntityInteractEvent $event): void
    {
        $entity = $event->getEntity();
        $player = $event->getPlayer();

        if ($entity instanceof Human) LobbyNPCManager::performNPCAction($entity, $player);
    }

    public function onInventoryItemClicked(InventoryTransactionEvent $event): void
    {
        // $event->cancel();
    }
}
