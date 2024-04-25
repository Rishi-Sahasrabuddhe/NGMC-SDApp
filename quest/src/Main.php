<?php

declare(strict_types=1);

namespace megarabyte\quest;

use megarabyte\afksafeguard\PlayerManager;
use megarabyte\quest\events\QuestListener;
use megarabyte\worldshandler\WorldHandler;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;
use pocketmine\world\WorldException;

class Main extends PluginBase
{
    private static PluginBase $instance;
    public static TaskScheduler $scheduler;
    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new QuestListener(), $this);

        $this->deleteWhackoHorseGames();

        self::$scheduler = $this->getScheduler();

        self::$instance = $this;
    }

    public function onDisable(): void
    {
        WorldHandler::unloadAllWorlds();
        $this->deleteWhackoHorseGames();
    }

    public static function getInstance(): PluginBase
    {
        return self::$instance;
    }

    public static function checkPlayerProgress(Player $player, ...$args)
    {
        $db = QuestData::getDatabase($player);
        if (($db->get('points') >= 10000) && $db->get('questProgress') == 2) {
            if ($args[0] instanceof \muqsit\invmenu\InvMenu) ($args[0])->__destruct();

            $db->edit('questProgress', 3);
            $db->edit('chapter', 2);
            foreach ($player->getWorld()->getPlayers() as $other)
                $other->sendMessage(TextFormat::BOLD . TextFormat::GOLD . $player->getName() .
                    TextFormat::RESET . " leveled up to Chapter 2!");
            $player->broadcastSound(new \pocketmine\world\sound\XpLevelUpSound(2));
            $player->sendTitle(TextFormat::GREEN . "LEVEL UP!", TextFormat::WHITE . 'New room unlocked!');
            $doorsUnlocked = $db->get('doorsUnlocked');
            if (is_array($doorsUnlocked)) {
                $doorsUnlocked[] = \megarabyte\lobby\quest\doors\Door1::class;
                $db->edit('doorsUnlocked', $doorsUnlocked);
            }
        }
    }

    private function deleteWhackoHorseGames(?string $folderPath = null)
    {
        $folderPath = $folderPath ?? $this->getServer()->getDataPath() . 'worlds\\whack-o-horse\\players';

        if (!is_dir($folderPath)) {
            return false;
        }

        $contents = scandir($folderPath);
        foreach ($contents as $item) {
            if ($item != "." && $item != "..") {
                $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;

                if (is_dir($itemPath)) {
                    $this->deleteWhackoHorseGames($itemPath);
                } else {
                    unlink($itemPath);
                }
            }
        }
    }
}
