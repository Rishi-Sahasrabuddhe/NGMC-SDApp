<?php

declare(strict_types=1);

namespace megarabyte\quest\games\WhackoHorse;

use megarabyte\eventsafeguard\PlayerEventSafeGuard;
use megarabyte\npc\HumanNPC;
use megarabyte\worldshandler\WorldHandler;
use megarabyte\quest\games\GamesPluginBase;
use megarabyte\quest\QuestData;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\sound\PopSound;
use pocketmine\world\World;

class WhackoHorseGame extends Task
{
    private Player $player;
    private array $horseLoc = [];
    private \megarabyte\quest\Main $main;

    function __construct(Player $player)
    {
        $this->player = $player;
        $this->main = \megarabyte\quest\Main::getInstance();
        PlayerEventSafeGuard::removeAllListeners($player, WhackoHorseListener::class);
        $this->main->getServer()->getPluginManager()->registerEvents(new WhackoHorseListener($this), \megarabyte\quest\Main::getInstance());


        $world = WorldHandler::getWorldByString(WorldHandler::duplicateWorld("preset\\whack-o-horse-random", "whack-o-horse\\players", "whack-o-horse-" .
            preg_replace('/[^A-Za-z0-9]+/', '_', trim(preg_replace('/_+/', '_', $player->getName()), '_'))));
        $player->teleport(new Position(0, 12, 0, $world));
        $this->setGameInventory($player);

        $this->horseLoc = [
            new HorseLocation(new Location(-2.5, 11, 0.5, $world, 90, 90)),
            new HorseLocation(new Location(-1.5, 11, -1.5, $world, 90, 90)),
            new HorseLocation(new Location(0.5, 11, -2.5, $world, 90, 90)),
            new HorseLocation(new Location(2.5, 11, -1.5, $world, 90, 90)),
            new HorseLocation(new Location(3.5, 11, 0.5, $world, 90, 90)),
            new HorseLocation(new Location(2.5, 11, 2.5, $world, 90, 90)),
            new HorseLocation(new Location(0.5, 11, 3.5, $world, 90, 90)),
            new HorseLocation(new Location(-1.5, 11, 2.5, $world, 90, 90))
        ];

        GamesPluginBase::startHorseSpawnTask($this);
    }



    public function getPlayer(): Player
    {
        return $this->player;
    }

    private function setGameInventory(Player $player = null)
    {
        $player = $player ?? $this->player;
        $inv = $player->getInventory();
        $db = QuestData::getDatabase($player);
        $inv->clearAll();
        $inv->setItem(0, VanillaItems::WOODEN_SWORD());
        $inv->setItem(4, VanillaItems::EMERALD()
            ->setCustomName("Stats")
            ->setLore([
                "Chapter: " . strval($db->get("chapter")),
                "Leather: " . strval($db->get("leather")),
                "Points: " . strval($db->get("points"))
            ]));
        $inv->setItem(8, VanillaItems::BLAZE_ROD()->setCustomName(TextFormat::RED . "Back"));
    }

    private function spawnRandomHorse(): void
    {
        $horseLocation = $this->getRandomHorseLocation();

        if ($horseLocation === null) {
            return;
        }
        $world = $this->player->getWorld();

        try {
            $horse = new WOHHorse($horseLocation);
        } catch (\pocketmine\utils\AssumptionFailedError $e) {
            return;
        }

        $horse->broadcastSound(new PopSound(0.5), [$this->player]);
        $horse->setSilent();
        $horse->teleport($horseLocation);
        $horse->lookAt(new Position(0, 12, 0, $world));
        $horse->spawnTo($this->player);
    }

    public function destructHorse(Player $player, WOHHorse $horse)
    {
        $activeTime = time() - $horse->getCreationTime();

        $leather = (15 - $activeTime);

        if ($leather < -100) $leather = -100;

        $db = QuestData::getDatabase($player);

        $db->edit(
            'leather',
            $db->get('leather') + $leather
        );

        if ($leather < 0) $player->sendActionBarMessage(
            TextFormat::RED .
                "Be faster next time! You just lost " . abs($leather) . " leather!"
        );

        $player->broadcastSound(new \pocketmine\world\sound\ExplodeSound(), [$player]);

        foreach ($this->horseLoc as $horseLocation) {
            if ($horseLocation->getLocation()->equals($horse->getInitialLocation())) {
                $horseLocation->setUsability(true);
                break;
            }
        }

        if ($db->get('questProgress') == 2 && $db->get('leather') >= 200) {
            $player->sendActionBarMessage('Go back to the lobby and buy some points off the Leather Worker!');
            $db->edit('questProgress', 3);
        }

        $horse->kill();
        $this->setGameInventory($player);
    }


    private function getRandomHorseLocation(): ?Location
    {
        $availableLocations = array_filter($this->horseLoc, function ($class) {
            return $class->getUsability();
        });

        $randomKey = empty($availableLocations) ? null : array_rand($availableLocations);

        if (isset($availableLocations[$randomKey])) {
            $location = ($availableLocations[$randomKey])->getLocation();
            ($availableLocations[$randomKey])->setUsability(false);
            return $location;
        } else return null;
    }

    public function onRun(): void
    {
        for ($i = 0; $i < random_int(0, 1); $i++) $this->spawnRandomHorse();
    }
}
