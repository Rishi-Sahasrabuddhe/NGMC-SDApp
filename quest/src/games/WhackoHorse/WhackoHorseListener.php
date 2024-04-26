<?php

declare(strict_types=1);

namespace megarabyte\quest\games\WhackoHorse;

use megarabyte\eventsafeguard\PlayerEventSafeGuard as PESG;
use megarabyte\lobby\LobbyConstants;
use megarabyte\quest\QuestData;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\ExplodeSound;

class WhackoHorseListener implements Listener
{
    private WhackoHorseGame $game;

    public function __construct(WhackoHorseGame $game)
    {
        $this->game = $game;
    }

    public function onHorseAttack(EntityDamageByEntityEvent $event)
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();

        if ($damager === null) return;
        if (!$damager instanceof Player) return;
        if (!PESG::playerHasListener($damager, self::class) || !$entity instanceof WOHHorse) return;


        $this->game->destructHorse($damager, $entity);
        return;
    }

    public function onPlayerBlockBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if (!PESG::playerHasListener($player, self::class)) return;
        if ($player->getGamemode() !== GameMode::CREATIVE()) {
            $event->cancel();
        }
    }

    public function onInventoryUpdate(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        if (!PESG::playerHasListener($player, self::class)) return;
        if ($player->getGamemode() !== GameMode::CREATIVE) $event->cancel();
    }

    public function playerInteractEvent(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK || $action === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
            $itemTypeId = $item->getTypeId();

            switch ($itemTypeId) {
                case VanillaItems::BLAZE_ROD()->getTypeId():
                    foreach (WOHHorse::getAllHorses() as $horse) {
                        $this->game->destructHorse($player, $horse);
                    }
                    LobbyConstants::sendPlayerToSpawn($player);
                    break;
                case VanillaItems::EMERALD()->getTypeId():
                    $db = QuestData::getDatabase($player);
                    $player->sendMessage(
                        TextFormat::BOLD . "Chapter: " . TextFormat::RESET . strval($db->get('chapter')) . "\n" .
                            TextFormat::BOLD . "Leather: " . TextFormat::RESET . strval($db->get('leather')) . "\n" .
                            TextFormat::BOLD . "Points: " . TextFormat::RESET . strval($db->get('points')) . "\n"
                    );
                    $player->selectHotbarSlot(0);
                    break;
            }
        }
    }
}
