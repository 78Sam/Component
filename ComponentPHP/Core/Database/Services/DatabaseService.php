<?php

declare(strict_types=1);

namespace Core\Database\Services;

use Core\Components\Models\Component;
use Core\Database\Exceptions\NoConnectionException;
use Core\Logging\Services\LoggingService;

class DatabaseService
{
    public static ?DatabaseService $instance = null;

    public ?\PDO $connection = null;

    private LoggingService $loggingService;

    private function __construct(
        LoggingService $loggingService,
    ) {
        $this->loggingService = $loggingService;
    }

    public static function getInstance(LoggingService $loggingService)
    {
        if (static::$instance === null) {
            static::$instance = new DatabaseService($loggingService);
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

    /**
     * @return list<array<string, mixed>>
     */
    public function getArrayResult(\PDOStatement $statement): array
    {
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @template T
     *
     * @param class-string<T> $model
     * @param \Closure(array<string, mixed> $row): array<string, mixed> $normaliser
     *
     * @return list<T>
     */
    public function getResult(\PDOStatement $statement, string $model, $normaliser = null): array
    {
        // There is really no hand holding here, no checking of args or constructors, let it fail through

        $rows = $this->getArrayResult($statement);
        if ($rows === []) {
            return [];
        }

        $reflectionModel = new \ReflectionClass($model);
        $reflectionConstructorArguments = $reflectionModel->getConstructor()?->getParameters() ?? [];

        $constructorArguments = [];
        foreach ($reflectionConstructorArguments as $argument) {
            $constructorArguments[$argument->name] = true;
        }

        $result = [];
        foreach ($rows as $row) {
            if ($normaliser !== null) {
                $row = $normaliser($row);
            }
            foreach ($row as $key => $_) {
                if (!array_key_exists($key, $constructorArguments)) {
                    unset($row[$key]);
                }
            }
            $result[] = $reflectionModel->newInstance(...$row);
        }

        return $result;
    }

    /**
     * @template T
     *
     * @param class-string<T> $model
     * @param \Closure(array<string, mixed> $row): array<string, mixed> $normaliser
     *
     * @return ?T
     */
    public function getOneOrNullResult(\PDOStatement $statement, string $model, $normaliser = null): ?object
    {
        $results = $this->getResult($statement, $model, $normaliser);

        return count($results) !== 1 ? null : $results[0];
    }

    /**
     * @return array<string, mixed>
     */
    public function getOneOrNullArrayResult(\PDOStatement $statement): ?array
    {
        $arrayResults = $this->getArrayResult($statement);

        return count($arrayResults) !== 1 ? null : $arrayResults[0];
    }

    private function runQuery(string $queryString, array $values): ?\PDOStatement
    {
        $statement = $this->connection->prepare($queryString);
        if ($statement === false) {
            return null;
        }

        try {
            $result = $statement->execute($values);
        } catch (\Throwable $e) {
            $this->loggingService->log("Failed query: '{$statement->queryString}'", LoggingService::LEVEL_ERROR);

            throw $e;
        }

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
            $query->fill($variable, ":{$variable}", raw: true);
        }

        return $values;
    }
}
