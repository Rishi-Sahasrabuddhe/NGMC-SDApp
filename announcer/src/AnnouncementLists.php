<?php

declare(strict_types=1);

namespace announcer;

use permanentstorage\Database;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AnnouncementLists
{
    private Database $announcementsData;
    private array $allAnnouncements;
    public function __construct()
    {
        $path = "plugins\announcer\src\data";
        $name = "announcements";
        $this->announcementsData = Database::getDatabaseFromPath("$path\\$name.json") ?? new Database($name, $path);
        $this->allAnnouncements = $this->announcementsData->read();
    }


    public function addAnnouncement(Announcement $announcement): void
    {
        $allAnnouncements[$announcement->getName()] = [
            'name' => $announcement->getName(),
            'announcement' => $announcement->getAnnouncement(),
            'usability' => $announcement->getUsability()
        ];
        $this->announcementsData->write($allAnnouncements);
    }

    public function deleteAnnouncement(Announcement $announcement): void
    {
        unset($this->allAnnouncements[$announcement->getName()]);
        $this->announcementsData->write($this->allAnnouncements);
    }

    public function editAnnouncement(Announcement $originalAnnouncement, string $newMessage)
    {
        $name = $originalAnnouncement->getName();
        $this->deleteAnnouncement($originalAnnouncement);
        $this->addAnnouncement(new Announcement($name, $newMessage));
    }

    public function getAnnouncementFromName(string $name): ?Announcement
    {
        return $this->allAnnouncements[$name] ?? null;
    }

    public function getAllAnnouncements(): string
    {
        return empty($this->allAnnouncements)
            ? "No announcements"
            : implode("\n - ", array_map(fn ($a) => $a['name'], $this->allAnnouncements));
    }
}
