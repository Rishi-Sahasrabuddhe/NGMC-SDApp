<?php

declare(strict_types=1);

namespace thebridge\commands;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\World;
use pocketmine\math\Vector3;

class CreateBridge
{
    /**
     * Creates a The Bridge compatible bridge at set location of a world
     * Mid-point of the bridge would be 0,15,0
     * The bridge would go along the x axis, 30 blocks each way
     * 
     * @param World $world World type to build the bridge in.
     * @return bool True if bridge successfully created, false if bridge creation failed.
     */

    static function createBridge(World $world): bool
    {
        if (!str_contains($world->getFolderName(), "tb-")) {
            return false;
        }

        // Create mid-point
        for ($y = 1; $y < 20; $y++) {
            $ypos = new Vector3(0, $y, 0);
            $world->setBlock($ypos, VanillaBlocks::STAINED_CLAY());

            for ($x = 1; $x < 30; $x++) {
                $redpos = new Vector3($x, $y, 0);
                $world->setBlock($redpos, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED));
            }
            for ($x = -1; $x > -30; $x--) {
                $bluepos = new Vector3($x, $y, 0);
                $world->setBlock($bluepos, VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BLUE));
            }
        }

        return true;
    }
}
