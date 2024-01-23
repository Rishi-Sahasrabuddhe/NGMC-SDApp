<?php

declare(strict_types=1);

namespace announcer;

use messageservice\Error;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AnnouncementHandler implements Listener
{
    private Player|CommandSender $player;
    private Announcement $announcement;
    private AnnouncementLists $announcementsDatabase;

    public function __construct()
    {
        $this->announcementsDatabase = new AnnouncementLists();
    }

    function confirmAnnouncement(Announcement $announcement, Player|CommandSender $player)
    {
        $this->announcement = $announcement;
        $this->player = $player;
        $player->sendMessage(TextFormat::BOLD . "Confirm the addition of the following announcement.\n" . TextFormat::RESET .
            TextFormat::UNDERLINE . "Name: " . TextFormat::RESET . $announcement->getName() .
            TextFormat::UNDERLINE . "\nAnnouncement: " . $announcement->getAnnouncement());
        $player->sendMessage("Type " . TextFormat::GREEN . "confirm add " . TextFormat::RESET . "to confirm your request.");
        $player->sendMessage("Type " . TextFormat::RED . "cancel " . TextFormat::RESET . "to cancel your request.");
    }

    private function announcementConfirmed(Announcement $announcement)
    {
        $this->announcementsDatabase->addAnnouncement($announcement);
    }

    function confirmDeletion(Announcement $announcement, Player|CommandSender $player)
    {
        $this->announcement = $announcement;
        $this->player = $player;
        $player->sendMessage(TextFormat::BOLD . "Confirm the deletion of the following announcement.\n" . TextFormat::RESET .
            TextFormat::UNDERLINE . "Name: " . TextFormat::RESET . $announcement->getName() .
            TextFormat::UNDERLINE . "\nAnnouncement: " . $announcement->getAnnouncement());
        $player->sendMessage("Type " . TextFormat::GREEN . "confirm delete " . TextFormat::RESET . "to confirm your request.");
        $player->sendMessage("Type " . TextFormat::RED . "cancel " . TextFormat::RESET . "to cancel your request.");
    }

    private function deletionConfirmed(Announcement $announcement)
    {
        $this->announcementsDatabase->deleteAnnouncement($announcement);
    }
    function confirmEdit(Announcement $announcement, Player|CommandSender $player)
    {
        $this->announcement = $announcement;
        $this->player = $player;
        $player->sendMessage(TextFormat::BOLD . "Confirm the addition of the following announcement.\n" . TextFormat::RESET .
            TextFormat::UNDERLINE . "Name: " . TextFormat::RESET . $announcement->getName() .
            TextFormat::UNDERLINE . "\nAnnouncement: " . $announcement->getAnnouncement());
        $player->sendMessage("Type " . TextFormat::GREEN . "confirm edit " . TextFormat::RESET . "to confirm your request.");
        $player->sendMessage("Type " . TextFormat::RED . "cancel " . TextFormat::RESET . "to cancel your request.");
    }

    private function editConfirmed(Announcement $announcement)
    {
        $this->announcementsDatabase->editAnnouncement($announcement, $announcement->getAnnouncement());
    }

    function onChatConfirm(PlayerChatEvent $event)
    {
        if ($event->getPlayer() === $this->player) {
            $message = strtolower($event->getMessage());
            $this->player->sendMessage("Conformation $message sent!");
            if ($message === "cancel") $this->announcement->delete();
            elseif ($message === "confirm add") $this->announcementConfirmed($this->announcement);
            elseif ($message === "confirm delete") $this->deletionConfirmed($this->announcement);
            elseif ($message === "confirm edit") $this->editConfirmed($this->announcement);
            else {
                $unknownCommand = new Error("Please either CONFIRM or CANCEL this operation!");
                $this->player->sendMessage($unknownCommand->sendError());
            }
            $event->cancel();
        }
    }
}
