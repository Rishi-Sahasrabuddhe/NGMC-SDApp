<?php

declare(strict_types=1);

namespace megarabyte\quest\games\WhackoHorse;

use pocketmine\entity\Location;

class HorseLocation
{
    private Location $location;
    private bool $usability;
    function __construct(Location $Location, bool $usability = true)
    {
        $this->location = $Location;
        $this->usability = $usability;
    }

    function getLocation(): Location
    {
        return $this->location;
    }
    function getUsability(): bool
    {
        return $this->usability;
    }
    function setUsability(bool $usability): void
    {
        $this->usability = $usability;
    }
}
