<?php

declare(strict_types=1);

namespace megarabyte\announcer;


use messageservice\Error;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
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
        if ($player instanceof ConsoleCommandSender) {
            $this->announcementConfirmed($announcement);
        } else {
            $this->player = $player;
            $player->sendMessage(TextFormat::BOLD . "Confirm the addition of the following announcemeTnt.\n" . TextFormat::RESET .
                TextFormat::UNDERLINE . "Name: " . TextFormat::RESET . $announcement->getName() .
                TextFormat::UNDERLINE . "\nAnnouncement: " . $announcement->getAnnouncement());
            $player->sendMessage("Type " . TextFormat::GREEN . "confirm add " . TextFormat::RESET . "to confirm your request.");
            $player->sendMessage("Type " . TextFormat::RED . "cancel " . TextFormat::RESET . "to cancel your request.");
        }
    }

    private function announcementConfirmed(Announcement $announcement)
    {
        $this->announcementsDatabase->addAnnouncement($announcement);
    }

    function confirmDeletion(Announcement $announcement, Player|CommandSender $player)
    {
        $this->announcement = $announcement;
        if ($player instanceof ConsoleCommandSender) {
            $this->deletionConfirmed($announcement);
        } else {
            $this->player = $player;
            $player->sendMessage(TextFormat::BOLD . "Confirm the deletion of the following announcement.\n" . TextFormat::RESET .
                TextFormat::UNDERLINE . "Name: " . TextFormat::RESET . $announcement->getName() .
                TextFormat::UNDERLINE . "\nAnnouncement: " . $announcement->getAnnouncement());
            $player->sendMessage("Type " . TextFormat::GREEN . "confirm delete " . TextFormat::RESET . "to confirm your request.");
            $player->sendMessage("Type " . TextFormat::RED . "cancel " . TextFormat::RESET . "to cancel your request.");
        }
    }

    private function deletionConfirmed(Announcement $announcement)
    {
        $this->announcementsDatabase->deleteAnnouncement($announcement);
    }
    function confirmEdit(Announcement $announcement, Player|CommandSender $player)
    {
        $this->announcement = $announcement;
        if ($player instanceof ConsoleCommandSender) {
            $this->editConfirmed($announcement);
        } else {
            $this->player = $player;
            $player->sendMessage(TextFormat::BOLD . "Confirm the addition of the following announcement.\n" . TextFormat::RESET .
                TextFormat::UNDERLINE . "Name: " . TextFormat::RESET . $announcement->getName() .
                TextFormat::UNDERLINE . "\nAnnouncement: " . $announcement->getAnnouncement());
            $player->sendMessage("Type " . TextFormat::GREEN . "confirm edit " . TextFormat::RESET . "to confirm your request.");
            $player->sendMessage("Type " . TextFormat::RED . "cancel " . TextFormat::RESET . "to cancel your request.");
        }
    }

    private function editConfirmed(Announcement $announcement)
    {
        $this->announcementsDatabase->editAnnouncement($announcement, $announcement->getAnnouncement());
    }

    function onChatConfirm(PlayerChatEvent $event)
    {
        if ($event->getPlayer() !== $this->player) {
            $event->getPlayer()->sendMessage(Error::INTERNAL);
            return;
        }

        $message = strtolower($event->getMessage());
        $announcementName = $this->announcement->getName();
        $confirmationMessage = null;
        if ($message === "cancel") {
            $this->announcement->delete();
            $confirmationMessage = "Operation canceled!";
        } elseif ($message === "confirm add") {
            $this->announcementConfirmed($this->announcement);
            $confirmationMessage = "Announcement $announcementName added!";
        } elseif ($message === "confirm delete") {
            $this->deletionConfirmed($this->announcement);
            $confirmationMessage = "Announcement $announcementName deleted!";
        } elseif ($message === "confirm edit") {
            $this->editConfirmed($this->announcement);
            $confirmationMessage = "Announcement $announcementName edited!";
        } else {
            $unknownCommand = new Error("Please either CONFIRM or CANCEL this operation!");
            $this->player->sendMessage($unknownCommand->sendError());
        }

        if (is_string($confirmationMessage)) {
            $this->player->sendMessage("Confirmation: $confirmationMessage");
        }

        $event->cancel();
    }
}
