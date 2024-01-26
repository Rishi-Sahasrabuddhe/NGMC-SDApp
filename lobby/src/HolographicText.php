<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\world\particle\FloatingTextParticle;

use megarabyte\worldshandler\WorldHandler;

class HolographicText
{
    private string $title;
    private string $text;
    private Position $position;
    private ?array $players = null;

    function __construct(string $title, string $text, Position $position, ?array $players = null)
    {
        $this->title = $title;
        $this->text = $text;
        $this->position = $position;


        $world = $position->getWorld();
        $this->players = $players ?? $world->getPlayers();

        $floatingText = new FloatingTextParticle($text, $title);
        $world->addParticle($position, $floatingText, $this->players);
    }

    /**
     * Update the holographic text with new title, text, and set of players.
     *
     * @param array|null $newPlayers
     * @param string $newTitle
     * @param string $newText
     */
    public function updateHolographic(?array $newPlayers = null, string $newTitle = "", string $newText = ""): void
    {
        $this->players = $newPlayers ?? $this->players;
        if ($newTitle !== "") $this->title = $newTitle;
        if ($newText !== "") $this->title = $newText;

        $this->deleteHolographic();

        $floatingText = new FloatingTextParticle($this->text, $this->title);
        $this->position->getWorld()->addParticle($this->position, $floatingText, $this->players);
    }

    /**
     * Delete the holographic.
     */
    public function deleteHolographic(): void
    {
        // Create an empty FloatingTextParticle to remove the existing holographic text.
        $emptyFloatingText = new FloatingTextParticle('');
        $this->position->getWorld()->addParticle($this->position, $emptyFloatingText, $this->players);
    }
}
