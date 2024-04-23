<?php

declare(strict_types=1);

namespace megarabyte\quest;

use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\Scoreboard;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\session\PlayerManager;
use Ifera\ScoreHud\session\PlayerSession;

use megarabyte\quest\QuestData;

use pocketmine\player\Player;

class QuestScoreboard extends Scoreboard
{

    private const QUEST_CHAPTER = 'quest.chapter';
    private const QUEST_LEATHER = 'quest.leather';
    private const QUEST_POINTS = 'quest.points';

    public static function getLeaderboard(Player $player): Scoreboard
    {

        $scoreboard = new Scoreboard(
            (new PlayerSession($player)),
            (PlayerManager::get($player))->getScoreboard()->getLines(),
            (PlayerManager::get($player))->getScoreboard()->getTags()
        );

        return $scoreboard;
    }
}
