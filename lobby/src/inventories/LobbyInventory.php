<?php

declare(strict_types=1);

namespace megarabyte\lobby\inventories;

use megarabyte\lobby\Main;
use megarabyte\quest\games\WhackoHorse\WhackoHorseGame;
use megarabyte\quest\QuestData;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class LobbyInventory extends PlayerInventory implements Listener
{
    function __construct(Player $player, false|int $selectHotbarSlot = false)
    {
        if (is_int($selectHotbarSlot)) $player->selectHotbarSlot($selectHotbarSlot);
        $inventory = $player->getInventory();
        $inventory->clearAll();

        $db = QuestData::getDatabase($player);

        if ($db->get("questProgress") >= 1) $inventory->setItem(1, VanillaItems::LEATHER()->setCustomName("Quest Manager"));
        if ($db->get("questProgress") >= 2) {
            $inventory->setItem(4, VanillaItems::COMPASS()->setCustomName("Game Teleporter"));
            $inventory->setItem(0, VanillaItems::EMERALD()
                ->setCustomName("Stats")
                ->setLore([
                    "Chapter: " . strval($db->get("chapter")),
                    "Leather: " . strval($db->get("leather")),
                    "Points: " . strval($db->get("points"))
                ]));
        }
    }



    public function playerInteractEvent(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $item = $event->getItem();
        $player = $event->getPlayer();
        $main = Main::getInstance();

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
        }
    }
}
