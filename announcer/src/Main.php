<?php

declare(strict_types=1);

namespace megarabyte\announcer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\HandlerListManager;
use pocketmine\utils\TextFormat;

use commands\GeneralCommandChecker as GCC;
use messageservice\Error;

class Main extends PluginBase
{

    private AnnouncementLists $announcementsDatabase;

    public function onEnable(): void
    {
        $this->getScheduler()->scheduleRepeatingTask(new AnnouncementBroadcaster(), 20 * mt_rand(300, 600));
        $this->announcementsDatabase = new AnnouncementLists();
    }


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case 'announcement':
                $announcementHandler = new AnnouncementHandler();
                HandlerListManager::global()->unregisterAll($this);

                $acceptedValues = "Accepted values:\n" .
                    "- add (creates a new announcement)\n" .
                    "- remove (deletes an announcement)\n" .
                    "- edit (edits existing announcement)\n" .
                    "- view (views announcement message)";

                if (!GCC::checkIfHasPermission($sender, $command)) {
                    $sender->sendMessage(GCC::permissionValidationMessage($command));
                    return true;
                }
                if (!isset($args[0])) $args[0] = 'help';
                $name = isset($args[1]) ? strtolower($args[1]) : null;
                switch ($args[0]) {
                    case 'add':
                        if (!isset($name)) {
                            $error = new Error("Error: Name not found!");
                            $sender->sendMessage($error->sendError() . " Please include an announcement name.");
                            return false;
                        }
                        if (!isset($args[2])) {
                            $error = new Error("Error: Content not found!");
                            $sender->sendMessage($error->sendError() . " Please include an announcement message.");
                            return false;
                        }
                        if ($this->announcementsDatabase->getAnnouncementFromName($name) !== null) {
                            $sender->sendMessage((new Error("Error: Announcement already exists!"))->sendError());
                            return true;
                        }
                        $message = implode(' ', array_slice($args, 2));
                        $this->getServer()->getPluginManager()->registerEvents($announcementHandler, $this);
                        $announcementHandler->confirmAnnouncement(new Announcement($name, $message), $sender);
                        return true;
                    case 'remove':
                        if (!isset($name)) {
                            $error = new Error("Error: Name not found!");
                            $sender->sendMessage($error->sendError() . " Please include an announcement name.");
                            return false;
                        }
                        $this->getServer()->getPluginManager()->registerEvents($announcementHandler, $this);
                        $announcement = $this->announcementsDatabase->getAnnouncementFromName($name);
                        if ($announcement === null) {
                            $sender->sendMessage((new Error("Error: Announcement does not exist!"))->sendError());
                            return true;
                        }
                        $announcementHandler->confirmDeletion($announcement, $sender);
                        return true;
                    case 'edit':
                        if (!isset($name)) {
                            $error = new Error("Error: Name not found!");
                            $sender->sendMessage($error->sendError() . " Please include an announcement name.");
                            return false;
                        }
                        if ($this->announcementsDatabase->getAnnouncementFromName($name) === null) {
                            $error = new Error("Error: Announcement does not exist!");
                            $sender->sendMessage($error->sendError() . " Please check the name and try again.");
                        }
                        if (!isset($args[2])) {
                            $error = new Error("Error: Content not found!");
                            $sender->sendMessage($error->sendError() . " Please include an announcement message.");
                            return false;
                        }

                        $message = implode(' ', array_slice($args, 2));
                        $this->getServer()->getPluginManager()->registerEvents($announcementHandler, $this);
                        $announcementHandler->confirmEdit(new Announcement($name, $message), $sender);
                        return true;
                    case 'view':
                        if ($name === "all") {
                            $sender->sendMessage(TextFormat::UNDERLINE . "Available announcements: \n"
                                . TextFormat::RESET . $this->announcementsDatabase->getAllAnnouncements());
                            return true;
                        }
                        if (!isset($name)) {
                            $error = new Error("Error: Name not found!");
                            $sender->sendMessage($error->sendError() . " Please include an announcement name.");
                            return false;
                        }
                        $announcement = $this->announcementsDatabase->getAnnouncementFromName($name);
                        if ($announcement === null) {
                            $error = new Error("Error: Announcement does not exist!");
                            $sender->sendMessage($error->sendError() . " Please check the name and try again.");
                            return false;
                        } else {
                            $sender->sendMessage(TextFormat::UNDERLINE . $announcement->getName() .
                                TextFormat::RESET . "\n" . $announcement->getAnnouncement());
                        }
                        return true;
                    case 'help':
                        $sender->sendMessage("Manipulate server announcements using '/announcement'.");
                        $sender->sendMessage($acceptedValues);
                        return false;
                    default:
                        $actionNotFound = new Error("Action not found!\n");
                        $sender->sendMessage($actionNotFound->sendError() . $acceptedValues);
                        return false;
                }
            default:
                $sender->sendMessage(Error::unknownCommandError($command)->sendError());
                break;
        }
        return true;
    }
}
