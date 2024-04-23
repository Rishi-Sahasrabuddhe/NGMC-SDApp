<?php

declare(strict_types=1);

namespace megarabyte\quest;

use megarabyte\quest\events\QuestListener;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;

class Main extends PluginBase
{
    private static PluginBase $instance;
    public static TaskScheduler $scheduler;
    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new QuestListener(), $this);

        self::$scheduler = $this->getScheduler();

        self::$instance = $this;
    }

    public function onDisable(): void
    {
        $this->deleteWhackoHorseGames();
    }

    public static function getInstance(): PluginBase
    {
        return self::$instance;
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
