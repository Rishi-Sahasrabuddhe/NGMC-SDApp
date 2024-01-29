<?php

declare(strict_types=1);

namespace megarabyte\announcer;

use megarabyte\messageservice\Error;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AnnouncementBroadcaster extends Task
{
    public function onRun(): void
    {
        $announcements = new AnnouncementLists();
        $server = Server::getInstance();
        $players = $server->getOnlinePlayers();
        $announcement = $announcements->getRandomAnnouncement(true);
        if ($announcement === null) {
            $server->getLogger()->info((new Error("Error: No announcements available!"))->sendError());
            return;
        }
        foreach ($players as $player) {
            $player->sendToastNotification(
                TextFormat::YELLOW . TextFormat::BOLD . "ANNOUNCEMENT",
                $announcement->getAnnouncement()
            );
        }
    }
}
