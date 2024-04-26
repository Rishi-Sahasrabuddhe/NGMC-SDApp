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

    static function crawickRealm(Player $player): Book
    {

        $book = new Book("Crawick's Realm", TF::RED . TF::OBFUSCATED . "ADMIN");
        $book->addCharacter(new Character("Jackie", "Hoffman", TextFormat::DARK_GREEN))
            ->addCharacter(new Character("Tom", "Fielding", TextFormat::BLUE))
            ->addCharacter(new Character("Starford", "Crawick", TextFormat::RED))
            ->addCharacter(new Character("You", $player->getName(), TextFormat::DARK_GRAY))
            ->formatBookContentPages(self::crawickRealmContent($book));
        return $book;
    }

    private static function crawickRealmContent(Book $book): string
    {
        $jackie = $book->getCharacterByName('Jackie');
        $playerChar = $book->getCharacterByName('You');
        $starford = $book->getCharacterByName('Starford');
        $tom = $book->getCharacterByName("Tom");
        $gold = TextFormat::GOLD;
        return <<<EOT
        {$jackie->say()}Oh? I see you have gained access to the forbidden corridor.
        {$playerChar->say()}Ohh, is that what the room is-
        {$jackie->say()}Quiet. Ya're on the venture of bein' a hero now. You can't say all this nonsense anymore.
        {$playerChar->say()}Umm sure. What should I do now?
        {$jackie->say()}The corridor you opened has two branchin' rooms. They are both Elite Housin' rooms hosted by {$tom->getFormattedFirstName()} {$tom->getLastName()}{$jackie->getColour()}: the richest man in the town, second to {$starford->getFormattedLastName()}{$jackie->getColour()}, and {$starford->getFormattedLastName()}{$jackie->getColour()}'s biggest hater.
        {$playerChar->say()}So you want me to go into those rooms and what? Befriend {$tom->getLastName()}{$jackie->getColour()}?
        {$jackie->say()}Well, yes-
        {$playerChar->say()}No way! Isn't he just a rich brat who was part of the reason for the town's oppression?
        {$jackie->say()}Yer, he may be, but he's the only way we can take him down. You up for the challenge?
        {$playerChar->say()}Well, yes!
        {$jackie->say()}Alrighty then, {$playerChar->getFormattedLastName()}{$jackie->getColour()}. You need$gold 50,000{$jackie->getColour()} points to open each door. I know Whack-o-Horse is boring, but a new way of earning leather is behind each of those doors. So keep grinding, and I shall see you... very soon.
        EOT;
    }
}
