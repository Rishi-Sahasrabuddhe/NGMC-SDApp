<?php

declare(strict_types=1);

namespace megarabyte\writtenbooks;

class Character
{
    private string $firstName;
    private string $lastName;
    private string $colour;

    public function __construct(string $firstName, string $lastName, string $colour)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->colour = $colour;
    }

    public function say(): string
    {
        return $this->colour . $this->firstName . ": ";
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getFormattedFirstName(): string
    {
        return $this->colour . $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFormattedLastName(): string
    {
        return $this->colour . $this->lastName;
    }

    public function getColour(): string
    {
        return $this->colour;
    }

    public function setColour(string $colour): void
    {
        $this->colour = $colour;
    }
}
