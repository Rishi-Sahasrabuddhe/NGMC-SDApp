<?php

declare(strict_types=1);

namespace megarabyte\permanentstorage;

class Database
{
    private string $databaseName;

    private string $filePath;
    private array $defaultValues = [];

    private string $databasePath;

    /**
     * Database constructor.
     *
     * @param string $databaseName The name of the database.
     * @param string $filePath The base path where the database file is stored.
     */
    public function __construct(string $databaseName, string $filePath, array $defaultValues = [])
    {
        $this->databaseName = $databaseName;
        $this->filePath = $filePath;
        $this->defaultValues = $defaultValues;

        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $this->databasePath = $filePath . DIRECTORY_SEPARATOR . $databaseName . ".json";

        if (!empty($defaultValues)) $this->write($defaultValues);
    }

    /**
     * Get the name of the database.
     *
     * @return string The name of the database.
     */
    public function getName(): string
    {
        return $this->databaseName;
    }

    /**
     * Get the base path where the database file is stored.
     *
     * @return string The base path of the database file.
     */
    public function getPath(): string
    {
        return $this->filePath;
    }

    /**
     * Read the contents of the database file.
     *
     * @return array The decoded array of key-value pairs.
     */
    public function read(): array
    {
        return json_decode(file_get_contents($this->databasePath), true) ?? [];
    }

    /**
     * Write data to the database file.
     *
     * @param array $data The data to be written to the file.
     */
    public function write(array $data): void
    {
        file_put_contents($this->databasePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Add a key-value pair to the database.
     *
     * @param string $key The key.
     * @param string $value The value.
     */
    public function add(string $key, string $value): void
    {
        $data = $this->read();
        $data[$key] = $value;
        $this->write($data);
    }

    /**
     * Delete a key-value pair from the database.
     *
     * @param string $key The key to be deleted.
     */
    public function delete(string $key): void
    {
        $data = $this->read();
        unset($data[$key]);
        $this->write($data);
    }

    /**
     * Edit the value of a key in the database.
     *
     * @param string $key The key to be edited.
     * @param mixed $newValue The new value for the key.
     */
    public function edit(string $key, mixed $newValue): void
    {
        $data = $this->read();
        if (array_key_exists($key, $data)) {
            $data[$key] = $newValue;
        }
        $this->write($data);
    }

    /**
     * Get the value of a key from the database.
     *
     * @param string $key The key to retrieve the value for.
     *
     * @return string|null The value associated with the key, or null if the key is not found.
     */
    public function get(string $key): mixed
    {
        $data = $this->read();
        return $data[$key] ?? null;
    }

    public function sortByKey()
    {
        $data = $this->read();
        ksort($data);
        $this->write($data);
    }

    /**
     * Get a Database instance from a specified path.
     *
     * @param string $path The path to the database file.
     *
     * @return Database|null The Database instance, or null if the path is invalid.
     */
    public static function getDatabaseFromPath(string $path): Database
    {
        $pathInfo = pathinfo($path);
        $filePath = $pathInfo['dirname'];
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $databaseName = $pathInfo['filename'];
        $databasePath = $filePath . DIRECTORY_SEPARATOR . $databaseName . ".json";

        // Create the file if it doesn't exist
        if (!file_exists($databasePath)) {
            file_put_contents($databasePath, '[]');
        }

        return new Database($databaseName, $filePath);
    }

    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }
    public function setDefaultValues(array $defaultValues)
    {
        $this->defaultValues = $defaultValues;
    }
}
