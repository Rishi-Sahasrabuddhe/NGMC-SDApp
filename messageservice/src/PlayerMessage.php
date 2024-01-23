<?php

declare(strict_types=1);

namespace messageservice;

use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

/**
 * The PlayerMessage class provides a set of utility methods for sending various types of messages to players.
 */
class PlayerMessage
{
    /**
     * Sends a title and subtitle to a player.
     *
     * @param Player               $player   The player to whom the title and subtitle will be sent.
     * @param string|Translatable $title    The main title text.
     * @param string|Translatable $subtitle The subtitle text.
     * @param int                  $fadeIn   Duration of the fade-in effect in ticks (default: -1 for no specific duration).
     * @param int                  $stay     Duration the title stays on the screen in ticks (default: -1 for no specific duration).
     * @param int                  $fadeOut  Duration of the fade-out effect in ticks (default: -1 for no specific duration).
     *
     * @see Player::sendTitle()
     */
    static function sendTitle(Player $player, string|Translatable $title, string|Translatable $subtitle, int $fadeIn = -1, int $stay = -1, int $fadeOut = -1)
    {
        $player->sendTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
    }

    /**
     * Sends a popup message to a player.
     *
     * @param Player               $player  The player to whom the popup message will be sent.
     * @param string|Translatable $message The message text.
     *
     * @see Player::sendPopup()
     */
    static function sendPopup(Player $player, string|Translatable $message)
    {
        $player->sendPopup($message);
    }

    /**
     * Sends a tip message to a player.
     *
     * @param Player $player  The player to whom the tip message will be sent.
     * @param string|Translatable $message The message text.
     *
     * @see Player::sendTip()
     */
    static function sendTip(Player $player, string|Translatable $message)
    {
        $player->sendTip($message);
    }

    /**
     * Sends a toast notification to a player.
     *
     * @param Player $player The player to whom the toast notification will be sent.
     * @param string|Translatable $title The title text of the toast notification.
     * @param string|Translatable $body The text of the toast notification.
     *
     * @see Player::sendToastNotification()
     */
    static function sendToastNotif(Player $player, string|Translatable $title, string|Translatable $body)
    {
        $player->sendToastNotification($title, $body);
    }

    /**
     * Sends a subtitle to a player.
     *
     * @param Player $player The player to whom the subtitle will be sent.
     * @param string|Translatable $subtitle  The subtitle text.
     *
     * @see Player::sendSubTitle()
     */
    static function sendSubTitle(Player $player, string|Translatable $subtitle)
    {
        $player->sendSubTitle($subtitle);
    }

    /**
     * Resets titles for a player.
     *
     * @param Player $player The player for whom titles will be reset.
     *
     * @see Player::resetTitles()
     */
    static function resetTitles(Player $player)
    {
        $player->resetTitles();
    }
}
