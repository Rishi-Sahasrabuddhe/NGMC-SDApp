<?php

declare(strict_types=1);

namespace megarabyte\writtenbooks;

use pocketmine\item\VanillaItems;
use pocketmine\item\WritableBookBase;
use pocketmine\item\WritableBookPage;
use pocketmine\player\Player;

use megarabyte\writtenbooks\Character;

class Book extends WritableBookBase
{
    private string $title;
    private string $author;
    private array $pages;
    private array $characters;

    public function __construct(string $title, string $author, array $characters = [])
    {
        $this->title = $title;
        $this->author = $author;
        $this->characters = $characters ?? [];

        $this->pages = $this->getPages();
    }

    function setPageText(int $pageId, string $pageText): static
    {
        if (!$this->pageExists($pageId)) {
            $this->addPage($pageId);
        }

        $this->pages[$pageId] = new WritableBookPage($pageText);
        return $this;
    }

    function getTitle(): string
    {
        return $this->title;
    }

    function getAuthor(): string
    {
        return $this->author;
    }

    function setTitle(string $newTitle): void
    {
        $this->title = $newTitle;
    }

    function setAuthor(string $newAuthor): void
    {
        $this->author = $newAuthor;
    }

    function addCharacter(Character $character): void
    {
        $this->characters[] = $character;
    }

    function removeCharacter(Character $character): void
    {
        $key = array_search($character, $this->characters, true);
        if ($key !== false) {
            unset($this->characters[$key]);
        }
    }

    public function getCharacters(): array
    {
        return $this->characters ?? [];
    }

    public function openBook(Player $player): void
    {
        new OpenBook($player, $this);
    }
}
