<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use megarabyte\eventsafeguard\PlayerEventSafeGuard;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

use megarabyte\messageservice\HolographicText;
use megarabyte\quest\QuestData;
use megarabyte\worldshandler\WorldHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class LobbyConstants
{
    static function getWelcomeHolographicText(Player $player): string
    {
        if (QuestData::getDataArray($player)["questProgress"] === 0) $CTA = TextFormat::AQUA . "Click HERE to begin your questline!" . TextFormat::RESET;
        else $CTA = TextFormat::AQUA . "Click on the Leather in your inventory to learn more about your quest!" . TextFormat::RESET;
        return "Welcome to the LeatherGames Network, the ultimate destination for players seeking
        servers with original names! Win games and be rewarded with the coveted currency, LEATHER. This resource opens the gates to exclusive
        areas within the lobby, granting you access to thrilling quests that will put your skills to the test. Engage with fellow gamers,
        form alliances, and forge unforgettable memories.\n
        $CTA \n\n
        LeatherGames Network: where YOU carve your journey...";
    }

    public static HolographicText $infoHolographic;

    static function getLobbySpawnpoint(): Position
    {
        return new Position(0.5, 14, 0.5, WorldHandler::getWorldByString("lobby"));
    }

    static function getLobbyWorld(): World
    {
        return WorldHandler::getWorldByString("lobby");
    }

    static function sendPlayerToSpawn(Player $player): void
    {
        $player->setRotation(-90, 180);
        WorldHandler::joinWorld("lobby", $player, self::getLobbySpawnpoint());
        $player->setSpawn(self::getLobbySpawnpoint());
        PlayerEventSafeGuard::config($player);
        \megarabyte\lobby\Main::configureLobby($player);
    }

    public static function processHolographicText(string $text, int $length): string
    {
        $text = str_replace(["\r\n", "\r"], '', preg_replace('/[ \t]+/', ' ', str_replace("\t", "", $text)));
        $text = str_replace('\n', PHP_EOL, $text);


        $words = explode(" ", $text);

        $output = "";
        $currentLineLength = 0;

        foreach ($words as $word) {
            $wordLength = strlen($word);
            if ($currentLineLength + $wordLength > $length) {
                $output .= "\n";
                $currentLineLength = 0;
            }

            $output .= $word . " ";
            $currentLineLength += $wordLength + 1;
        }

        return trim($output);
    }
}
