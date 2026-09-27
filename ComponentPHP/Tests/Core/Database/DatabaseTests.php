<?php

declare(strict_types=1);

namespace Tests\Core\Database;

use Core\Components\Services\ComponentService;
use Core\Database\Services\DatabaseService;
use Core\Tests\AbstractTest;
use Core\Tests\Attributes\Test;

class DatabaseTests extends AbstractTest
{
    private ComponentService $componentService;
    private DatabaseService $database;

    #[\Override]
    public function setup(): void
    {
        $this->componentService = new ComponentService();
        $this->database = DatabaseService::getInstance();
        $this->database->connect('sqlite::memory:');
    }

    #[\Override]
    public function preTest(Test $test): void
    {
        $this->database->beginTransaction();
    }

    #[\Override]
    public function postTest(Test $test): void
    {
        $this->database->rollbackTransaction();
    }

    #[Test('Create the testing database table', priority: 1, skipPreTest: true, skipPostTest: true)]
    public function createTableTest(): void
    {
        $createTableQuery = <<<'SQL'
            CREATE TABLE Test(
                id INTEGER PRIMARY KEY,
                data TEXT NOT NULL
            );
            SQL;

        $result = $this->database->queryString($createTableQuery);

        static::assertNotEquals($result, null, 'No PDO statement returned from query');
    }

    #[Test('Insert sample data into the test database', priority: 2, skipPreTest: true, skipPostTest: true)]
    public function insertStubDataTest(): void
    {
        $insertQuery = <<<'SQL'
            INSERT INTO Test (data) VALUES
            ('data_item_1'),
            ('data_item_2'),
            ('data_item_3');
            SQL;

        $result = $this->database->queryString($insertQuery);

        static::assertNotEquals($result, null, 'No PDO statement returned from query');
    }

    #[Test('Select all from database using a component')]
    public function selectAllTest(): void
    {
        $selectAllQuery = $this->componentService->get(
            'select_all',
            'Tests/Core/Database/Include/Components/queries.sql',
        );

        $result = $this->database->query($selectAllQuery);

        static::assertNotEquals($result, null, 'No PDO statement returned from query');

        $numRows = count($result->fetchAll());
        static::assertEquals($numRows, 3, "Query returned {$numRows} rows instead of 3");
    }

    #[Test('Select from database using a component and where clause')]
    public function selectWhereTest(): void
    {
        $selectWhereQuery = $this->componentService->get(
            'select_all_where',
            'Tests/Core/Database/Include/Components/queries.sql',
        )->fill('value', 'data_item_2');

        $result = $this->database->query($selectWhereQuery);

        static::assertNotEquals($result, null, 'No PDO statement returned from query');

        $row = $result->fetch(\PDO::FETCH_ASSOC);

        static::assertEquals($row, ['id' => 2, 'data' => 'data_item_2'], 'Failed to select the specific row');
    }

    #[Test('Multiple condition select where')]
    public function nestedSelectWhereTest()
    {
        $nestedSelectWhereQuery = $this->componentService->get(
            'select_all_where_nested',
            'Tests/Core/Database/Include/Components/queries.sql',
        );

        $orCondition = $this->componentService
            ->get('or_condition', 'Tests/Core/Database/Include/Components/queries.sql')
            ->fill('value1', 'data_item_1')
            ->fill('value2', 'data_item_3');

        $nestedSelectWhereQuery->fill('condition', $orCondition);

        $result = $this->database->query($nestedSelectWhereQuery);

        static::assertNotEquals($result, null, 'No PDO statement returned from query');

        $rows = $result->fetchAll(\PDO::FETCH_ASSOC);
        $numRows = count($rows);

        static::assertEquals($numRows, 2, "Query returned {$numRows} rows instead of 2");

        if ($rows[0]['id'] === 1) {
            static::assertEquals($rows[0], ['id' => 1, 'data' => 'data_item_1'], 'Fetch 1 incorrect');
            static::assertEquals($rows[1], ['id' => 3, 'data' => 'data_item_3'], 'Fetch 2 incorrect');
        } else {
            static::assertEquals($rows[1], ['id' => 1, 'data' => 'data_item_1'], 'Fetch 1 incorrect');
            static::assertEquals($rows[0], ['id' => 3, 'data' => 'data_item_3'], 'Fetch 2 incorrect');
        }
    }
}
