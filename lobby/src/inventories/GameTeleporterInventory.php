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

class GameTeleporterInventory extends PlayerInventory implements Listener
{

    function __construct(?Player $player)
    {
        if ($player === null) return;
        $qp = QuestData::getDatabase($player)->get("questProgress");
        $inv = $player->getInventory();
        $inv->clearAll();
        if ($qp >= 2) {
            if ($qp === 2) $player->sendTip("Join a Whack-o-Horse game in Slot 1!");
            $inv->setItem(0, VanillaItems::SHAPER_ARMOR_TRIM_SMITHING_TEMPLATE()->setCustomName("Whack-o-horse"));
        }
        $inv->setItem(8, VanillaItems::BLAZE_ROD()->setCustomName(TextFormat::RED . "Back"));
    }



    public function playerInteractEvent(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $item = $event->getItem();
        $player = $event->getPlayer();
        $main = Main::getInstance();

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($item->getTypeId() === VanillaItems::SHAPER_ARMOR_TRIM_SMITHING_TEMPLATE()->getTypeId()) {
                new WhackoHorseGame($player);
            }



            if ($item->getTypeId() === VanillaItems::BLAZE_ROD()->getTypeId()) {
                $main->configureLobby($player);
            }
        }
    }
}
