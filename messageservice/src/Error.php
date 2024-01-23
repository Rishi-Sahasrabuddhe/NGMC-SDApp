<?php

declare(strict_types=1);

namespace messageservice;

use pocketmine\command\Command;
use pocketmine\utils\TextFormat;

class Error
{
    private string $customError;

    /**
     * Creates a custom error message that can be used anywhere!
     * Use Error::sendError() to get the custom error message.
     * 
     * @param string $errorBody The content of the error message
     * @param bool $resetColour Default: True - Resets the colour back to default after the message.
     */
    function __construct(string $errorBody, bool $resetColour = true)
    {
        if ($resetColour) {
            $this->customError = TextFormat::RED . $errorBody . TextFormat::RESET;
        } else {
            $this->customError = TextFormat::RED . $errorBody;
        }
    }

    function sendError(): string
    {
        return $this->customError;
    }

    public const NOPERM = TextFormat::RED . "Error: You do not have permission to run this command!";
    public const NOTPLAYER = TextFormat::RED . "Error: Only players can run this command!";
    public const INTERNAL = TextFormat::RED . "Error: An internal error occured! " .
        TextFormat::RESET . "Please take a screenshot and report this to the developers.";

    static function unknownCommandError(Command $command): Error
    {
        return new Error("Error: Unknown command /" . $command->getName() . TextFormat::RESET . ". Please check the spelling and try again.");
    }
}
