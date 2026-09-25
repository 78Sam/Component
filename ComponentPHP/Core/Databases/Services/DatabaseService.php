<?php

declare(strict_types=1);

namespace Core\Databases\Services;

use Core\Components\Models\Component;

class DatabaseService
{
    public static ?DatabaseService $instance = null;

    public ?\PDO $connection = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new DatabaseService();
        }

        return static::$instance;
    }

    public function connect(string $dsn, ?string $username = null, ?string $password = null, ?array $options = null): self
    {
        $this->connection = new \PDO($dsn, $username, $password, $options);

        return $this;
    }

    public function query(Component $query): ?\PDOStatement
    {
        if ($this->connection === null) {
            throw new \Exception('Cannot query without first connecting to DB');
        }

        $values = $this->recurseComponent($query);
        $queryString = $query->render();

        $statement = $this->connection->prepare($queryString);
        if ($statement === false) {
            return null;
        }

        $result = $statement->execute($values);
        if ($result === false) {
            return null;
        }

        return $statement;
    }

    /**
     * @return array<string, string>
     */
    private function recurseComponent(Component $query): array
    {
        $values = [];
        foreach ($query->variableMap as $variable => $pseudonyms) {
            $socketValue = $query->sockets[array_first($pseudonyms)] ?? '';
            if ($socketValue instanceof Component) {
                $values += $this->recurseComponent($socketValue);

                continue;
            }

            $values[$variable] = $socketValue;
            $query->fill($variable, ":{$variable}");
        }

        return $values;
    }
}
