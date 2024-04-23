<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextFormat;

use megarabyte\writtenbooks\Book;
use megarabyte\writtenbooks\Character;

class LobbyBooks
{
    static function howToUseLeather(Player $player): Book
    {

        $book = new Book("Introduction to Leather", TF::RED . TF::OBFUSCATED . "ADMIN");
        $book->addCharacter(new Character("Jackie", "Hoffman", TextFormat::DARK_GREEN))
            ->addCharacter(new Character("Tom", "Fielding", TextFormat::BLUE))
            ->addCharacter(new Character("Starford", "Crawick", TextFormat::RED))
            ->addCharacter(new Character("You", $player->getName(), TextFormat::DARK_GRAY))
            ->formatBookContentPages(self::howToUseLeatherContent($book));
        return $book;
    }

    private static function howToUseLeatherContent(Book $book): string
    {
        $jackie = $book->getCharacterByName('Jackie');
        $playerChar = $book->getCharacterByName('You');
        $starford = $book->getCharacterByName('Starford');
        return <<<EOT
        {$jackie->say()}Oh well look-ey 'ere. Yar look like fresh meat.
        {$playerChar->say()}Erm yeah! Just came through the other day. Hi, I'm {$playerChar->getLastName()}.
        {$jackie->say()}Oi get your migrant hand away from me! The whole lot of you of are disgusting. Waddlin' in like you own the place-
        {$playerChar->say()}Well excuse you! I'm having such a bad day getting my-
        {$jackie->say()}Leather confiscated by the authorities. Yeah, they're takin' it all for 'emselves.
        {$playerChar->say()}What?
        {$jackie->say()}It's a {$starford->getFormattedLastName()}{$jackie->getColour()} monopoly. Got the 'hole town scrummaging 'round fo' the scraps. Got to a point where leather is the currency for barter!
        {$playerChar->say()}Oh no that's terri-
        {$jackie->say()}Oh save it. It's you lot who increase our misery by decreasing our leather supply. You really want to help?
        {$playerChar->say()}Yes. I do! It's outragous they oppress you like that!
        {$jackie->say()}Alrighty then, {$playerChar->getFormattedLastName()}{$jackie->getColour()}. Prove you're not all talk and join me in killing this tyrant!
        The best way to get leather is to play Whack-o-Horse. Click on the Game Teleporter in your 5th Hotbar Slot and queue into a game of Whack-o-Horse!
        EOT;
    }
}
