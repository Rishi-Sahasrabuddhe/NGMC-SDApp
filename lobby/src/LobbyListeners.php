<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use megarabyte\eventsafeguard\PlayerEventSafeGuard as PESG;
use megarabyte\lobby\inventories\GameTeleporterInventory;
use megarabyte\lobby\lobbynpcs\LobbyNPCManager;
use megarabyte\lobby\quest\doors\Door1;
use megarabyte\messageservice\HolographicText;
use megarabyte\quest\QuestData;
use megarabyte\worldshandler\WorldHandler;

use muqsit\invmenu\inventory\InvMenuInventory;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerEntityInteractEvent,
    PlayerInteractEvent,
    PlayerJoinEvent,
    PlayerMoveEvent,
    PlayerQuitEvent
};
use pocketmine\event\server\ServerEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\sound\PopSound;

class LobbyListeners implements Listener
{
    private Listener $instance;
    function __construct()
    {
        $this->instance = $this;
    }

    static function getInstance(): self
    {
        return self::$instance;
    }

    function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        LobbyConstants::sendPlayerToSpawn($player);

        (new LobbyNPCManager())->spawnLobbyNPCs($player);


        LobbyConstants::$infoHolographic = new HolographicText(
            "Welcome, " . $player->getName() . "!",
            LobbyConstants::processHolographicText(LobbyConstants::getWelcomeHolographicText($player), 80),
            new Position(-12, 14.5, 0.5, WorldHandler::getWorldByString("lobby")),
            [$player]
        );
        QuestData::getDatabase($player)->edit('inGame', true);

        $player->getHungerManager()->setSaturation(20);
        $player->getHungerManager()->setFood(20);
    }

    function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        PESG::removeAllListeners($player, self::class);
        QuestData::getDatabase($player)->edit('inGame', false);
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        $cause = $event->getCause();

        if ($entity instanceof Player && PESG::playerHasListener($entity, self::class)) {
            $event->cancel();
            if ($cause === EntityDamageEvent::CAUSE_VOID) LobbyConstants::sendPlayerToSpawn($entity);
        }
    }

    public function onPlayerEntityInteraction(PlayerEntityInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if (!PESG::playerHasListener($player, self::class)) return;
        $entity = $event->getEntity();
    }

    public function playerInteractEvent(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $item = $event->getItem();
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $main = Main::getInstance();

        if (!PESG::playerHasListener($player, self::class)) return;

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($item !== null && $item->getTypeId() === VanillaItems::LEATHER()->getTypeId()) {
                if (QuestData::getDataArray($player)["questProgress"] === 1) {
                    QuestData::getDatabase($player)->edit("questProgress", 2);
                    $player->getWorld()->addSound($player->getPosition(), new PopSound(), [$player]);
                    $player->sendTip("Click on the Game Teleporter in Slot 5!");
                    $main->configureLobby($player);
                }
                if (QuestData::getDataArray($player)["questProgress"] > 1 && QuestData::getDataArray($player)["questProgress"] < 4)
                    LobbyBooks::howToUseLeather($player)->openBook($player);
                if (QuestData::getDataArray($player)["questProgress"] >= 4 && QuestData::getDataArray($player)["questProgress"] < 6)
                    LobbyBooks::howToUseLeather($player)->openBook($player);
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
                if (QuestData::getDataArray($player)["questProgress"] === 0) {
                    QuestData::getDatabase($player)->edit("questProgress", 1);
                    $player->getWorld()->addSound($player->getPosition(), new PopSound(), [$player]);
                    $player->sendTip("Click on the Quest Manager in Slot 2!");
                    $main->configureLobby($player);
                }
            }
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        if (!PESG::playerHasListener($player, self::class)) return;

        $newLoca = $event->getTo();
        if (
            Door1::checkDoorUnlocked($player) == true &&
            abs($newLoca->getFloorX()) < 2 &&
            $newLoca->getFloorY() >= 12 && $newLoca->getFloorY() <= 15
        ) {
            if ($newLoca->getFloorZ() == 12) {
                Door1::teleportThroughDoor($player, true);
                return;
            }
            if ($newLoca->getFloorZ() == 14) {
                Door1::teleportBACKThroughDoor($player, true);
                return;
            }
        }
    }

    public function onPlayerBlockBreak(BlockBreakEvent $event): void
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

        if ($player->getGamemode() !== GameMode::CREATIVE && !$player->getCurrentWindow() instanceof InvMenuInventory) $event->cancel();
    }
}
