<?php

declare(strict_types=1);

namespace announcer;

use permanentstorage\Database;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

use Main;
use messageservice\Error;
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
        $server->getLogger()->info("Announcement " . $announcement->getName() . " sent as Toast!");
        foreach ($players as $player) {
            $player->sendToastNotification(
                TextFormat::YELLOW . TextFormat::BOLD . "ANNOUNCEMENT",
                $announcement->getAnnouncement()
            );
        }
    }
}
