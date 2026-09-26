<?php

declare(strict_types=1);

namespace Core\Database\Services;

use Core\Components\Models\Component;
use Core\Database\Exceptions\NoConnectionException;

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

    public function connect(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        ?array $options = null,
    ): self {
        $this->connection = new \PDO($dsn, $username, $password, $options);

        return $this;
    }

    public function queryString(string $query, array $values = []): ?\PDOStatement
    {
        if ($this->connection === null) {
            throw new NoConnectionException();
        }

        return $this->runQuery($query, $values);
    }

    public function query(Component $query): ?\PDOStatement
    {
        if ($this->connection === null) {
            throw new NoConnectionException();
        }

        $values = $this->prepareComponent($query);
        $queryString = $query->render();

        return $this->runQuery($queryString, $values);
    }

    public function beginTransaction(): void
    {
        if ($this->connection === null) {
            throw new NoConnectionException();
        }

        $this->connection->beginTransaction();
    }

    public function rollbackTransaction(): void
    {
        if ($this->connection === null) {
            throw new NoConnectionException();
        }

        $this->connection->rollBack();
    }

    public function commitTransaction(): void
    {
        if ($this->connection === null) {
            throw new NoConnectionException();
        }

        $this->connection->commit();
    }

    private function runQuery(string $queryString, array $values): ?\PDOStatement
    {
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
    private function prepareComponent(Component $query): array
    {
        $values = [];
        foreach ($query->variableMap as $variable => $pseudonyms) {
            $socketValue = $query->sockets[array_first($pseudonyms)] ?? '';
            if ($socketValue instanceof Component) {
                $values += $this->prepareComponent($socketValue);

                continue;
            }

            $values[$variable] = $socketValue;
            $query->fill($variable, ":{$variable}");
        }

        return $values;
    }
}
