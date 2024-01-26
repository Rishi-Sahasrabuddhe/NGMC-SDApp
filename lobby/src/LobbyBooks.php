<?php

declare(strict_types=1);

namespace megarabyte\lobby;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextFormat;

use megarabyte\lobby\introductiontoleatherbook\Pages;
use megarabyte\writtenbooks\Book;
use megarabyte\writtenbooks\Character;

class LobbyBooks
{
    static function howToUseLeather(Player $player): Book
    {

        $book = new Book("Introduction to Leather", TF::RED . TF::OBFUSCATED . "ADMIN");
        $book->addCharacter(new Character("Jackie", "Hoffman", TextFormat::GREEN));
        $book->addCharacter(new Character("Tom", "Fielding", TextFormat::BLUE));
        $book->addCharacter(new Character("Starford", "Crawick", TextFormat::RED));
        $book->addCharacter(new Character("Player", "Player", TextFormat::WHITE));

        $pages = new Pages($book, $player);
        $book->setPageText(1, $pages->pages[1]);
        return $book;
    }
}
