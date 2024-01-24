<?php

declare(strict_types=1);

namespace announcer;

class Announcement
{
    private string $name;
    private string $announcement;
    private bool $usability;

    /**
     * Creates a new announcement that will show up on the player's screen during gameplay
     * 
     * @param string $name Name of the announcement. Names must be unique.
     * @param string $announcement Content of the announcement.
     */
    function __construct(string $name, string $announcement, bool $usability = true)
    {
        $name = strtolower(str_replace(' ', '', preg_replace('/\s+/', '', $name)));
        $this->name = $name;
        $this->announcement = $announcement;
        $this->usability = $usability;
    }

    function getName(): string
    {
        return $this->name;
    }
    function getAnnouncement(): string
    {
        return $this->announcement;
    }
    function delete()
    {
        unset($this->announcement);
        (new AnnouncementLists())->deleteAnnouncement($this);
    }

    public function getUsability(): bool
    {
        return $this->usability;
    }
    public function setUsability(bool $usability): void
    {
        $this->usability = $usability;
    }
}
