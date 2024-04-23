<?php

declare(strict_types=1);

namespace megarabyte\quest\games;

use megarabyte\quest\games\WhackoHorse\WhackoHorseGame;
use megarabyte\quest\games\WhackoHorse\WhackoHorseListener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class GamesPluginBase extends PluginBase
{
    static function startHorseSpawnTask(WhackoHorseGame $game): void
    {
        \megarabyte\quest\Main::getInstance()->getScheduler()->scheduleRepeatingTask($game, rand(20, 50)); 
    }
}
