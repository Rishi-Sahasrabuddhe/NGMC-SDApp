<?php

declare(strict_types=1);

namespace megarabyte\announcer;

use megarabyte\permanentstorage\Database;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AnnouncementLists
{
    private Database $announcementsDB;
    private array $allAnnouncements;
    public function __construct()
    {
        $path = "plugins\announcer\src\data";
        $name = "announcements";
        $this->announcementsDB = Database::getDatabaseFromPath("$path\\$name.json") ?? new Database($name, $path);
        $this->allAnnouncements = $this->announcementsDB->read();
    }


    public function addAnnouncement(Announcement $announcement): void
    {
        $allAnnouncements = $this->announcementsDB->read();
        $allAnnouncements[$announcement->getName()] = [
            'name' => $announcement->getName(),
            'announcement' => $announcement->getAnnouncement(),
            'usability' => $announcement->getUsability()
        ];
        $this->announcementsDB->write($allAnnouncements);
        $this->announcementsDB->sortByKey();
    }

    public function deleteAnnouncement(Announcement $announcement): void
    {
        unset($this->allAnnouncements[$announcement->getName()]);
        $this->announcementsDB->write($this->allAnnouncements);
    }

    public function editAnnouncement(Announcement $originalAnnouncement, string $newMessage)
    {
        $name = $originalAnnouncement->getName();
        $this->deleteAnnouncement($originalAnnouncement);
        $this->addAnnouncement(new Announcement($name, $newMessage));
    }

    public function getAnnouncementFromName(string $name): ?Announcement
    {
        $announcements = $this->announcementsDB->read();

        if (isset($announcements[$name])) {
            $announcementData = $announcements[$name];
            return new Announcement(
                $announcementData['name'],
                $announcementData['announcement'],
                $announcementData['usability']
            );
        }
        return null;
    }

    public function getAllAnnouncements(): string
    {
        return empty($this->allAnnouncements)
            ? "No announcements"
            : implode("\n - ", array_map(fn ($a) => $a['name'], $this->allAnnouncements));
    }

    /**
     * Get a random announcement.
     *
     * @param bool $disableUsability Whether to disable usability for the selected announcement.
     * @return Announcement|null The randomly selected announcement, or null if there are no announcements.
     */
    public function getRandomAnnouncement(bool $disableUsability = false): ?Announcement
    {
        $announcements = $this->announcementsDB->read();
        if (empty($announcements)) return null;

        $usableAnnouncements = array_filter($announcements, function ($announcement) {
            return $announcement['usability'] === true;
        });

        if (empty($usableAnnouncements)) {
            foreach ($announcements as &$announcement) $announcement['usability'] = true;
            $this->announcementsDB->write($announcements);
            return $this->getRandomAnnouncement($disableUsability);
        }

        $randKey = array_rand($usableAnnouncements);
        $randAnnouncement = $usableAnnouncements[$randKey];

        if ($disableUsability) {
            $announcements[$randKey]['usability'] = false;
            $this->announcementsDB->write($announcements);
        }

        return new Announcement(
            $randAnnouncement['name'],
            $randAnnouncement['announcement']
        );
    }
}
