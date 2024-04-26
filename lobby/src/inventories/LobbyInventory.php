<?php

declare(strict_types=1);

namespace megarabyte\lobby\inventories;

use megarabyte\eventsafeguard\PlayerEventSafeGuard;
use megarabyte\lobby\LobbyBooks;
use megarabyte\lobby\Main;
use megarabyte\messageservice\Error;
use megarabyte\quest\QuestData;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class LobbyInventory extends PlayerInventory implements Listener
{
    private static bool|int $bookOpened = false; // reset at 3
    private static bool|int $statsSent = false; // reset at 2

    function __construct(?Player $player, false|int $selectHotbarSlot = false)
    {
        if (!$player instanceof Player) return;
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

        \megarabyte\lobby\inventories\LobbyInventory::resetSafeguard('book', 'stats');
    }



    public function playerInteractEvent(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if (!PlayerEventSafeGuard::playerHasListener($player, self::class)) return;


        $action = $event->getAction();
        $item = $event->getItem();
        $main = Main::getInstance();
        $db = QuestData::getDatabase($player);

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($item == null) return;
            switch ($item->getTypeId()) {
                case VanillaItems::COMPASS()->getTypeId():
                    new GameTeleporterInventory($player);
                    break;
                case VanillaItems::EMERALD()->getTypeId():
                    if (is_int(self::$statsSent)) {
                        if (self::$statsSent >= 1 && self::$statsSent < 2) self::$statsSent++;
                        elseif (self::$statsSent === 2) self::resetSafeguard('stats');
                        return;
                    }

                    if (self::$statsSent == false) self::$statsSent = 1;
                    $player->sendMessage(
                        TextFormat::BOLD . "Chapter: " . TextFormat::RESET . strval($db->get('chapter')) . "\n" .
                            TextFormat::BOLD . "Leather: " . TextFormat::RESET . strval($db->get('leather')) . "\n" .
                            TextFormat::BOLD . "Points: " . TextFormat::RESET . strval($db->get('points')) . "\n"
                    );
                    break;
                case VanillaItems::LEATHER()->getTypeId():
                    if (is_int(self::$bookOpened)) {
                        if (self::$bookOpened >= 1 && self::$bookOpened < 3) self::$bookOpened++;
                        elseif (self::$bookOpened === 3) self::resetSafeguard('book');
                        return;
                    }

                    if (self::$bookOpened == false) self::$bookOpened = 1;

                    if ($db->get('questProgress') === 1) {
                        $db->edit("questProgress", 2);
                        $player->getWorld()->addSound($player->getPosition(), new \pocketmine\world\sound\PopSound(), [$player]);
                        $player->selectHotbarSlot(2);
                        $player->sendTip("Click on the Game Teleporter in Slot 5!");
                        $main->configureLobby($player);
                    }
                    switch ($db->get('chapter')) {
                        case 1:
                            LobbyBooks::howToUseLeather($player)->openBook($player);
                            break;
                        case 2:
                            LobbyBooks::crawickRealm($player)->openBook($player);
                            break;
                    }
                    break;
            }
        }
    }

    public static function resetSafeguard(string ...$types)
    {
        foreach ($types as $type)
            switch ($type) {
                case 'book':
                    self::$bookOpened = false;
                    break;
                case 'stats':
                    self::$statsSent = false;
                    break;
            }
    }

    private static function sendSpamError(Player $player): void
    {
        $errorMessage = (new Error("Spam safeguard is blocking you from opening this item"))->sendError() . "\n" .
            "Please try again.";
        $player->sendMessage($errorMessage);
    }
}
