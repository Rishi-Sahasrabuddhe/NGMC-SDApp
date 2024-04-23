<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use megarabyte\eventsafeguard\PlayerEventSafeGuard as PESG;
use pocketmine\block\BaseSign;
use pocketmine\block\utils\SignText;
use pocketmine\block\utils\SignTextTest;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Server;

class SignInteractions implements Listener
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
    public function onSignChange(SignChangeEvent $event): void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if (!PESG::playerHasListener($player, self::class)) return;
        if ($block instanceof BaseSign) {
            $this->configureClickableSign($block);
        }
    }

    public function onSignClick(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $player = $event->getPlayer();
        $block = $event->getBlock();
        
        if (!PESG::playerHasListener($player, self::class)) return;

        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($block instanceof BaseSign) {
                $this->getSignText($block);
                $event->cancel();
            }
        }
    }

    private function getSignText(BaseSign $sign)
    {
        $text = $sign->getText()->getLine(0);
        Server::getInstance()->getLogger()->info($text);
    }

    private function configureClickableSign(BaseSign $sign)
    {
        $text = $sign->getText();
        $configLine = $text->getLine(0);
    }
}
