<?php

declare(strict_types=1);

namespace megarabyte\quest\games\WhackoHorse;

use megarabyte\worldshandler\WorldHandler;
use pocketmine\entity\Entity;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\Position;

class WhackoHorseGame
{
    private Player $player;
    private array $horsePos = [];

    function __construct(Player $player)
    {
        $this->player = $player;
        WorldHandler::duplicateWorld("preset\\whack-o-horse", "whack-o-horse\\players", "whack-o-horse-" . $player->getName());
        $world = WorldHandler::getWorldByString("whack-o-horse-" . $player->getName());
        $player->teleport(new Position(0, 12, 0, $world));
        $this->setGameInventory($player);

        // $this->horsePos = [
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false,
        //     new Position(0, 12, 0, $world) => false
        // ];
    }

    private function setGameInventory(Player $player = null)
    {
        $player = $player ?? $this->player;
        $inv = $player->getInventory();
        $inv->clearAll();
        $inv->setItem(0, VanillaItems::WOODEN_SWORD());
    }

    private function spawnRandomHorse(): void
    {
        $horsePos = $this->getRandomHorsePosition();
        $level = $this->player->getWorld();

        // Spawn a horse entity
        $horse = Entity::createEntity(EntityIds::HORSE, $level, new CompoundTag("", [
            new StringTag("id", EntityIds::HORSE),
            new StringTag("CustomName", "WhackoHorse"),
        ]));

        $horse->teleport($horsePos);
        $level->addEntity($horse);
    }

    private function getRandomHorsePosition(): Position
    {
        $availablePositions = array_keys(array_filter($this->horsePos, function ($value) {
            return $value === false;
        }));

        $randomKey = array_rand($availablePositions);

        // Make sure the key exists in the original array
        if (isset($this->horsePos[$randomKey])) {
            $this->horsePos[$randomKey] = true;
            return $randomKey;
        }
    }

    private function startHorseSpawnTask(): void
    {
        $task = new ClosureTask(function (int $currentTick): void {
            $this->spawnRandomHorse();
        });

        // Schedule the task to run every 3 to 7 seconds
        $this->player->getServer()->getScheduler()->scheduleRepeatingTask($task, mt_rand(60 * 3, 60 * 7)); // 60 ticks per second
    }
}
