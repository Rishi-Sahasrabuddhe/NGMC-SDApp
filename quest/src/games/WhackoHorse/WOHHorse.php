<?php

declare(strict_types=1);

namespace megarabyte\quest\games\WhackoHorse;

use pocketmine\entity\EntityFactory;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;

class WOHHorse extends Living
{
    private static World $world;
    private int $creationTime;
    private Location $initialLocation;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        $this->initialLocation = $location;
        (EntityFactory::getInstance())->register(self::class, function () use ($location): WOHHorse {
            return new WOHHorse($location);
        }, ['WOHHorse']);

        parent::__construct($location, $nbt);

        $this->creationTime = time();
        self::$world = $location->getWorld();
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::HORSE;
    }

    public function getName(): string
    {
        return "Whack-o-Horse Horse";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.6, 1.4);
    }

    public function getCreationTime(): int
    {
        return $this->creationTime;
    }

    public function getInitialLocation(): Location
    {
        return $this->initialLocation;
    }

    public static function getAllHorses(): array
    {
        $horses = [];
        foreach (self::$world->getEntities() as $entity) {
            if ($entity instanceof self) {
                $horses[] = $entity;
            }
        }
        return $horses;
    }
}
