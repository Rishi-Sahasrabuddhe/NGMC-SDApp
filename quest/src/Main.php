<?php

declare(strict_types=1);

namespace megarabyte\quest;

use megarabyte\quest\events\QuestListener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new QuestListener, $this);
    }
}
