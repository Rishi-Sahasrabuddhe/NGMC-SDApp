<?php

declare(strict_types=1);

namespace megarabyte\messageservice;

use pocketmine\world\Position;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;


class HolographicText
{
    /** @var string */
    private $title;

    /** @var string */
    private $text;

    /** @var Position */
    private $position;

    /** @var array|null */
    private $players;

    /**
     * Creates a new Holographic Text Particle
     *
     * @param string    $title
     * @param string    $text
     * @param Position  $position
     * @param array|null $players
     */
    public function __construct(string $title, string $text, Position $position, ?array $players = null)
    {
        $this->title = $title;
        $this->text = $text;
        $this->position = $position;
        $this->players = $players ?? $position->getWorld()->getPlayers();

        $floatingText = new FloatingTextParticle($text, $title);
        $position->getWorld()->addParticle($position, $floatingText, $this->players);
    }

    /**
     * Get the title of the holographic text.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title of the holographic text.
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the text of the holographic text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set the text of the holographic text.
     *
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * Get the position of the holographic text.
     *
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * Get the players associated with the holographic text.
     *
     * @return array|null
     */
    public function getPlayers(): ?array
    {
        return $this->players;
    }
}
