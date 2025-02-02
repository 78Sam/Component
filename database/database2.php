<?php


require_once(__DIR__ . "/../dir.php");
require_once($REQUIRE_ENV);


class Database {

    private string $ROOT_DIR;
    private $link;
    private bool $connection_success;
    private array $db_types;

    function __construct() {

        $this->ROOT_DIR = __DIR__;
        $this->connection_success = false;
        $this->db_types = [
            "sqlite3",
            "sqlite",
            "mysql"
        ];

        if (!file_exists("{$this->ROOT_DIR}/.env")) {
            $this->log("Failed to find .env file: '{$this->ROOT_DIR}/.env'");
            return;
        }

        $dotenv = Dotenv\Dotenv::createImmutable($this->ROOT_DIR);
        $dotenv->load();

        if (empty($_ENV["DB_TYPE"])) {
            $this->log("No 'DB_TYPE' env parameter set");
            return;
        }

        $db_type = $_ENV["DB_TYPE"];

        if (!is_string($db_type)) {
            $this->log("'DB_TYPE' not string: '{$db_type}'");
            return;
        }

        $db_type = strtolower($db_type);

        if (!in_array($db_type, $this->db_types)) {
            $this->log("Unknown 'DB_TYPE': '{$db_type}'");
            return;
        }

        switch ($db_type) {

            case "sqlite3":
            case "sqlite":
                $this->checkSqlite();
                break;

            // TODO: Add mysql database support
            
            default:
                $this->log("DB_TYPE not implemented");
                break;
        }

    }

    public function log(string $message): void {

        error_log(
            message: "database/database.php: '{$message}'"
        );

        return;

    }

    private function checkSqlite(): void {

        if (empty($_ENV["DATABASE"])) {
            $this->log("sqlite(3): No 'DATABASE' env parameter set");
            return;
        }

        $database = $_ENV["DATABASE"];

        if (!is_string($database)) {
            $this->log("sqlite(3): 'DATABASE' not string: '{$database}'");
            return;
        }

        $database = str_replace(
            search: ".db",
            replace: "",
            subject: $database
        );

        try {
            $this->link = new PDO(
                dsn: "sqlite:/{$this->ROOT_DIR}/databases/{$database}.db",
                options: [
                    PDO::ATTR_PERSISTENT=>true
                ]
            );
            $this->connection_success = true;
        } catch (\Throwable $th) {
            $this->log("Something went wrong...: '{$th}'");
        }

        return;

    }

    public function connectionStatus(): bool {
        return $this->connection_success;
    }

    public function query(string $query, ?array $query_params = []) {

        if (!$this->connection_success) {
            $this->log("Trying to execute sql on down database, return []");
            return [];
        }

        $start_time = microtime(true);

        $sql = file_get_contents($this->ROOT_DIR . "/sql/" . $query . ".sql");

        $mappings = [];
        foreach ($query_params as $param) {
            $sql = str_replace(
                search: "{{$param['key']}}", 
                replace: ":{$param['key']}",
                subject: $sql
            );
            $mappings[":{$param['key']}"] = $param["value"];
        }

        $this->log("Prepared statement: '{$sql}'");

        $stmt = $this->link->prepare(query: $sql);

        foreach ($mappings as $key => $value) {
            // $this->log("Binding: {$key} Value: {$value}");
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        // Mad bug where if you do an INSERT followed by a fetch, it will re-run the INSERT and dupe entries

        $rows = [];
        if (str_contains($sql, "SELECT")) {
            foreach ($stmt as $row) {
                $rows[] = $row;
            }
        }

        $query_time_taken = round(microtime(true)-$start_time, 5);

        $this->log("Query took {$query_time_taken}s");
        
        return $rows;
    }


}

// $db = new Database();
// // echo $db->connectionStatus();
// // $db->query("deleteAll");
// $db->query(
//     "createRow",
//     [
//         ["key"=>"id", "value"=>"1"],
//         ["key"=>"text", "value"=>"Hello!"],
//     ]
// );
// $db->query(
//     "createRow",
//     [
//         ["key"=>"id", "value"=>"2"],
//         ["key"=>"text", "value"=>"Hi!"],
//     ]
// );
// $res = $db->query("getEverything");
// // print_r($res);
// $db->query("deleteAll");

?>