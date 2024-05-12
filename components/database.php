<?php

// require_once($_SERVER["DOCUMENT_ROOT"] . "/dir.php");
require_once("../dir.php");
require_once($REQUIRE_ENV);


function getArgType($arg)
{
    switch (gettype($arg))
    {
        case 'double': return "d";
        case 'integer': return "i";
        case 'boolean': return "s";
        case 'NULL': return "s";
        case 'string': return "s";
        default:
            throw new \InvalidArgumentException('Argument is of invalid type '.gettype($arg));
    }
}


enum DatabaseType: int {
    case unknown = 0;
    case mysqli = 1;
    case sqlite = 2;

    public function fetch($result) {
        return match ($this) {
            DatabaseType::mysqli=>$result->fetch_assoc(),
            DatabaseType::sqlite=>$result->fetchArray(SQLITE3_ASSOC)
        };
    }

    public function prepareExecute($conn, string $statement, array $params = null) {

        if ($params) {
            foreach ($params as $param) {
                $statement = str_replace("{" . $param["key"] . "}", $this->marker($param["key"]), $statement);
            }
        }

        $stmt = $this->prepare($conn, $statement);

        if ($params) {

            foreach ($params as $param) {
                $this->bind($stmt, $param["key"], $param["value"]);
            }
        }

        return $this->execute($stmt);
    }

    private function marker(string $var = "") {
        return match ($this) {
            DatabaseType::mysqli=>"?",
            DatabaseType::sqlite=>":" . $var
        };
    }

    private function prepare($conn, string $statement) {
        return match ($this) {
            DatabaseType::mysqli=>$conn->prepare($statement),
            DatabaseType::sqlite=>$conn->prepare($statement)
        };
    }

    private function bind($statement, $key, $value) {
        return match ($this) {
            DatabaseType::mysqli=>$statement->bind_param($key, $value),
            DatabaseType::sqlite=>$statement->bindValue(":" . $key, $value)
        };
    }

    private function execute(SQLite3Stmt $statement) {
        return match ($this) {
            // DatabaseType::mysqli=>$statement->fetch_assoc(),
            DatabaseType::sqlite=>$statement->execute()
        };
    }
}


class Database {

    
    private $ROOT_DIR;
    private $link;
    private bool $connection_success;
    private DatabaseType $connection_type;


    /**
     * 
     * @param string $fallback URL that should be used if database connection fails
     * 
     */
    function __construct(string $fallback=null) {

        global $FOLDER_COMPONENTS;
        $this->ROOT_DIR = $FOLDER_COMPONENTS;
        $this->connection_success = false;

        if (file_exists($this->ROOT_DIR . "/.env")) {

            // LOAD ENV FILE

            $dotenv = Dotenv\Dotenv::createImmutable($this->ROOT_DIR);
            $dotenv->load();

            $this->connection_success = false;

            // CHECK IF RUNNING LOCALHOST

            $local_hosts = ["127.0.0.1", "::1"];
            $is_local_host = false;
            if (isset($_SERVER["REMOTE_ADDR"])) {
                $is_local_host = in_array($_SERVER["REMOTE_ADDR"], $local_hosts);
            }

            // ATTEMPT REMOTE MYSQL CONNECTION

            if (
                !$is_local_host &&
                isset($_ENV["HOSTNAME"]) && $_ENV["HOSTNAME"] !== "" &&
                isset($_ENV["DATABASE"]) && $_ENV["DATABASE"] !== "" &&
                isset($_ENV["USERNAME"]) && $_ENV["USERNAME"] !== "" &&
                isset($_ENV["PASSWORD"]) && $_ENV["PASSWORD"] !== ""
            ) {

                $this->connection_type = DatabaseType::mysqli;

                $HOSTNAME = $_ENV["HOSTNAME"];
                $DATABASE = $_ENV["DATABASE"];
                $USERNAME = $_ENV["USERNAME"];
                $PASSWORD = $_ENV["PASSWORD"];
                
                try {
                    $this->link = new mysqli($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);
                    $this->connection_success = $this->link instanceof mysqli && $this->link->connect_error === null;
                } catch (mysqli_sql_exception $e) {
                    $this->connection_success = false;
                }

            }

            // ATTEMPT LOCAL SQLITE3 CONNECTION

            if (
                !$this->connection_success && 
                isset($_ENV["LOCAL_DB"]) &&
                $_ENV["LOCAL_DB"] !== ""
            ) {

                $this->connection_type = DatabaseType::sqlite;

                $db_path = $this->ROOT_DIR . "/data/" . $_ENV["LOCAL_DB"];

                if (file_exists($db_path)) {
                    try {
                        $this->link = new SQLite3($db_path);
                        $this->connection_success = $this->link instanceof SQLite3;
                    } catch (SQLite3Exception $e) {
                        $this->connection_success = false;
                    }
                }

            }

        }

        if (!$this->connection_success) {
            $this->connection_type = DatabaseType::unknown;
        }

        // REDIRECT IF DB CONNECTION FAIL AND REDIRECT IS SET

        if (!$this->connection_success && $fallback) {
            header("Location: " . $fallback);
            exit();
        }

    }

    function __destruct() {

        if ($this->connection_success && $this->link) {
            $this->link->close();
        }

    }

    /**
     * 
     * @param string $query The filename of the query in components/data to be used
     * @param array $query_params Values to be replaced in the specified SQL file
     * 
     * @return array|null 2D array of rows, or null if no data
     * 
     */
    public function query(string $query, array $query_params = null): array|null {

        $sql = file_get_contents($this->ROOT_DIR . "/data/" . $query . ".sql");

        // "SELECT `password_hash` FROM `UserAccounts` WHERE `email`={email};"
        // query_params = [["key"=>"email", "value"=>"Sam@gmail.com"], ...]

        $result = $this->connection_type->prepareExecute($this->link, $sql, $query_params);

        $rows = [];
        while ($row = $this->connection_type->fetch($result)) {
            $rows[] = $row;
        }

        if (!count($rows)) {
            return null;
        }
        
        return $rows;
        
    }

    public function connectionStatus() {
        return $this->connection_success;
    }

    // public function getConnection(): mysqli|SQLite3|null {
    //     if ($this->connection_success) {
    //         return $this->link;
    //     }
    //     return null;
    // }

}

$db = new Database();
print_r($db->query(query: "getUserStatement", query_params: [["key"=>"email", "value"=>"Sam@gmail.com"]]));

?>