<?php

declare(strict_types=1);

namespace megarabyte\quest;

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
        self::$instance = $this;

        $this->getServer()->getPluginManager()->registerEvents(new QuestListener(), $this);

        self::deleteWhackoHorseGames();

        self::$scheduler = $this->getScheduler();
    }

    public function onDisable(): void
    {
        WorldHandler::unloadAllWorlds();
        self::deleteWhackoHorseGames();
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public static function checkPlayerProgress(Player $player, ...$args)
    {
        $db = QuestData::getDatabase($player);
        if (($db->get('points') >= 10000) && $db->get('questProgress') == 4) {
            if (isset($args) && $args[0] instanceof \muqsit\invmenu\InvMenu) ($args[0])->__destruct();
            $db->edit('questProgress', 5);
            $db->edit('chapter', 2);
            foreach ($player->getWorld()->getPlayers() as $other)
                $other->sendMessage(TextFormat::BOLD . TextFormat::GOLD . $player->getName() .
                    TextFormat::RESET . " leveled up to Chapter 2!");
            $player->broadcastSound(new \pocketmine\world\sound\XpLevelUpSound(1000), [$player]);
            $player->sendTitle(TextFormat::GREEN . "LEVEL UP!", TextFormat::WHITE . 'New room unlocked!');
            $player->sendToastNotification(
                "Message from " . TextFormat::DARK_GREEN . "Jackie Hoffman",
                "To view, open the Quest Manager in Slot 2!"
            );
            $player->selectHotbarSlot(2);
            $doorsUnlocked = $db->get('doorsUnlocked');
            if (is_array($doorsUnlocked)) {
                $doorsUnlocked[] = \megarabyte\lobby\quest\doors\Door1::class;
                $db->edit('doorsUnlocked', $doorsUnlocked);
            }
        }
    }

    public static function deleteWhackoHorseGames(string|Player|null $gameRef = null)
    {
        if ($gameRef instanceof Player) {
            $world = "whack-o-horse\\players\\whack-o-horse-" .
                preg_replace('/[^A-Za-z0-9]+/', '_', trim(preg_replace('/_+/', '_', $gameRef->getName()), '_'));
            WorldHandler::unloadWorld($world);
            $folderPath = self::getInstance()->getServer()->getDataPath() . "worlds\\{$world}";
        } else $folderPath = $gameRef ?? self::getInstance()->getServer()->getDataPath() . 'worlds\\whack-o-horse\\players';

        if (!is_dir($folderPath)) {
            return false;
        }

        $contents = scandir($folderPath);
        foreach ($contents as $item) {
            if ($item != "." && $item != "..") {
                $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;

                if (is_dir($itemPath)) {
                    self::deleteWhackoHorseGames($itemPath);
                } else {
                    unlink($itemPath);
                }
            }
        }
    }
}
