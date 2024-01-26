<?php

declare(strict_types=1);

namespace lobby\introductiontoleatherbook;

use pocketmine\player\Player;
use pocketmine\Server;

use megarabyte\writtenbooks\Book;
use megarabyte\writtenbooks\Character;

class Pages
{
    private Book $book;
    private Player $player;
    private ?array $characters;
    public array $pages = [];

    private Character $jackie;
    private Character $playerChar;
    private Character $starford;

    public function __construct(Book $book, Player $player)
    {
        $this->book = $book;
        $this->player = $player;

        $this->characters = $book->getCharacters();

        foreach ($this->characters as $character) Server::getInstance()->getLogger()->info("CHARACTER: " . $character->getFormattedFirstName());

        // Assuming you have a character named Jackie in the characters array
        $this->jackie = $this->findCharacterByName('Jackie');
        $this->playerChar = $this->findCharacterByName('Player');
        $this->starford = $this->findCharacterByName('Starford');

        $this->pages[1] = $this->generatePage1();
    }

    private function generatePage1(): string
    {
        return <<<EOT
            {$this->jackie->say()}Oh look. You look like fresh meat.
            {$this->playerChar->say()}Erm yeah! Just came through the other day. Hi, I'm {$this->player->getName()}.
            {$this->jackie->say()}Oi get your migrant hand away from me!     The whole lot of you of discusting. Waddling in like you own the place-
            {$this->playerChar->say()}Well excuse you! I'm having such a bad day getting my-
            {$this->jackie->say()}Leather confiscated by the authorities. Yeah, they're takin' it all for 'emselves.
            {$this->playerChar->say()}What?
            {$this->jackie->say()}It's a {$this->starford->getFormattedLastName()}{$this->jackie->getColour()} monopoly. Got the 'hole town scrummaging 'round fo' scraps. Got to a point where leather is the best currency for barter!
            {$this->playerChar->say()}Oh no that's terri-
            {$this->jackie->say()}Oh save it. It's you lot who increase our misery by decreasing our leather supply. You really want to help?
            {$this->playerChar->say()}Yes. I do! It's outragous they oppress you like that!
            {$this->jackie->say()}Alrighty then, {$this->player->getName()}. Prove you're not all talk and join me in killing the tyrant! 
        EOT;
    }

    private function findCharacterByName(string $name): ?Character
    {
        if ($this->characters !== null) {
            foreach ($this->characters as $character) {
                if (strtolower($character->getFirstName()) === strtolower($name)) {
                    return $character;
                }
            }
        }
        return null;
    }
}
