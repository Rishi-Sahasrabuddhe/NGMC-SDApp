<?php

declare(strict_types=1);

namespace megarabyte\eventsafeguard;

use megarabyte\lobby\inventories\GameTeleporterInventory;
use megarabyte\lobby\LobbyConstants;
use megarabyte\lobby\LobbyListeners;
use megarabyte\lobby\lobbynpcs\LobbyNPCListeners;
use megarabyte\lobby\SignInteractions;
use megarabyte\quest\events\QuestListener;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class PlayerEventSafeGuard extends PluginBase
{
    static function config(Player $player)
    {
        $player->{"activeListeners"} = [
            LobbyListeners::class,
            LobbyNPCListeners::class,
            GameTeleporterInventory::class,
            SignInteractions::class,
            QuestListener::class
        ];
    }


    /**
     * Adds listener to $player
     * @var Player $player Player you want to add listener to
     * @var string $listener Listener. Get from Listener::class
     */

    static function addListener(Player $player, string $listener)
    {
        $player->activeListeners[] = $listener;
    }

    /**
     * Removes listener from $player. Always add listener before removing listener. Ensure there is always at least one listener in the property.
     * @var Player $player Player you want to remove listener from
     * @var string $listener Listener. Get from Listener::class
     * @return bool Returns True if sucessfully removed and False if listener path is invalid.
     */
    static function removeListener(Player $player, string $listener): bool
    {
        $key = array_search($listener, $player->activeListeners, true);
        if ($key == false) return false;
        if (isset($player->activeListeners[$key])) unset($player->activeListeners[$key]);
        if (empty($player->activeListeners[$key])) {
            LobbyConstants::sendPlayerToSpawn($player);
            self::config($player);
        } else return false;
        return true;
    }

    /**
     * Adds listener to $player
     * @var Player $player Player you want to perform action to
     * @var string $listener Path to listener you want to add after removeing all listeners. Get from Listener::class
     */
    static function removeAllListeners(Player $player, string $fallbackListener)
    {
        $player->activeListeners = [];
        self::addListener($player, $fallbackListener);
    }

    static function playerHasListener(Player $player, string $listener): bool
    {
        if (in_array($listener, $player->activeListeners, true)) return true;
        else return false;
    }
}
