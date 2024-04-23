<?php

declare(strict_types=1);

namespace megarabyte\npc;

use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\Server;
use pocketmine\world\World;

class HumanNPC extends Human
{

    protected Location $location;
    protected Skin $skin;
    protected $nbt = null;

    function __construct(Location $location, Skin $skin, $nbt = null)
    {
        $this->location = $location;
        $this->skin = $skin;
        $this->nbt = $nbt;

        EntityFactory::getInstance()->register(Human::class, function (World $world, \pocketmine\nbt\tag\CompoundTag $nbt) use ($location, $skin): HumanNPC {
            return new HumanNPC($location, $skin, $nbt);
        }, ['Human']);

        parent::__construct($location, $skin, $nbt);
    }

    public static function getSkinFromImage(string $path)
    {
        $img = imagecreatefrompng(Server::getInstance()->getDataPath() . $path);
        $bytes = '';

        for ($y = 0; $y < imagesy($img); $y++) {
            for ($x = 0; $x < imagesx($img); $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int) ($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($img);

        return new Skin("Standard_CustomSlim", $bytes);
    }

    public function getNPCLocation(): Location
    {
        return $this->location;
    }

    public function getNPCSkin()
    {
        return $this->nbt;
    }

    public function getNPCNBT(): Skin
    {
        return $this->skin;
    }
}
