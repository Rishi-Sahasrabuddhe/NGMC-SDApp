<?php

declare(strict_types=1);

namespace megarabyte\writtenbooks;

use pocketmine\item\WritableBookBase;
use pocketmine\player\Player;

use megarabyte\writtenbooks\Character;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\item\WritableBookPage;
use pocketmine\Server;

class Book extends WritableBookBase
{
    private static ?self $instance = null;

    private string $title;
    private string $author;
    public array $pages;
    private array $characters;

    public function __construct(string $title, string $author, array $characters = [])
    {
        $this->title = $title;
        $this->author = $author;
        $this->characters = $characters ?? [];
        $this->pages = $this->getPages();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setTitle(string $newTitle): void
    {
        $this->title = $newTitle;
    }

    public function setAuthor(string $newAuthor): void
    {
        $this->author = $newAuthor;
    }

    public function addCharacter(Character $character): self
    {
        $this->characters[] = $character;
        return $this;
    }


    public function removeCharacter(Character $character): void
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
    public function getCharacterByName(string $firstName): ?Character
    {
        if ($this->characters !== null) {
            foreach ($this->characters as $character) {
                if (strtolower($character->getFirstName()) === strtolower($firstName)) {
                    return $character;
                }
            }
        }
        return null;
    }

    public function setLocalPageText(int $pageId, string $pageText): self
    {
        if (!$this->pageExists($pageId)) {
            $this->addPage($pageId);
        }

        $this->pages[$pageId] = new WritableBookPage($pageText);
        return $this;
    }

    public function openBook(Player $player): void
    {
        new OpenBook($player, $this);
    }

    public function formatBookContentPages(string $content, bool $action = false): void
    {
        $lines = explode("\n", $content);
        $result = [];
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $currentLine = '';
            foreach ($words as $word) {
                if ($word === "[/np]") {
                    $word = "";
                    $result = trim($currentLine);
                    $currentLine = '';
                } else {
                    if (strlen($currentLine . $word) > 798) {
                        $result[] = trim($currentLine);
                        $currentLine = $word;
                    } else $currentLine .= $word . ' ';
                }
            }
            $result[] = trim($currentLine);
        }

        if ($action) $this->pages = array_merge($this->pages, $result);
        elseif (!$action) foreach ($result as $id => $page) {
            $this->setLocalPageText($id, $page);
            // $this->setPageText($id, $page);
        }
        else new \Error("Action not bool");
    }

    public function convertToItem(): Item
    {
        $item = VanillaItems::WRITTEN_BOOK();
        return $item;
    }
}
