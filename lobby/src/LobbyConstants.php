<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\World;

use megarabyte\worldshandler\WorldHandler;

class LobbyConstants
{

    private const WELCOMEHOLOGRAPHICTEXT = "Welcome to the LeatherGames Network, the ultimate destination for players seeking
    servers with original names! Win games and be rewarded with the coveted currency, LEATHER. This resource opens the gates to exclusive
    areas within the lobby, granting you access to thrilling quests that will put your skills to the test. Engage with fellow gamers,
    form alliances, and forge unforgettable memories.\n
    Eager to delve deeper into the lore of LeatherGames? Click the leather icon in your hotbar to get started.\n\n
    The LeatherGames Network: where YOU carve your journey...";
    public HolographicText $infoHolographic;

    public function __construct()
    {
        $this->infoHolographic = new HolographicText(
            "Welcome!",
            self::processHolographicText(self::WELCOMEHOLOGRAPHICTEXT, 80),
            new Position(-12, 14.5, 0.5, WorldHandler::getWorldByString("lobby"))
        );
    }


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
