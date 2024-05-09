<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/dir.php");
require_once($REQUIRE_ENV);


class Database {

    
    private $ROOT_DIR;
    private $link;
    private bool $connection_success;
    private string $connection_type;


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

            $dotenv = Dotenv\Dotenv::createImmutable($this->ROOT_DIR);
            $dotenv->load();

            $this->connection_success = false;

            $local_hosts = ["127.0.0.1", "::1"];
            $is_local_host = in_array($_SERVER["REMOTE_ADDR"], $local_hosts);

            if (
                !$is_local_host &&
                isset($_ENV["HOSTNAME"]) && $_ENV["HOSTNAME"] !== "" &&
                isset($_ENV["DATABASE"]) && $_ENV["DATABASE"] !== "" &&
                isset($_ENV["USERNAME"]) && $_ENV["USERNAME"] !== "" &&
                isset($_ENV["PASSWORD"]) && $_ENV["PASSWORD"] !== ""
            ) {

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

                if ($this->connection_success) {
                    $this->connection_type = "remote";
                }
            }

            if (
                !$this->connection_success && 
                isset($_ENV["LOCAL_DB"]) &&
                $_ENV["LOCAL_DB"] !== ""
            ) {

                $db_path = $this->ROOT_DIR . "/data/" . $_ENV["LOCAL_DB"];

                $this->link = new SQLite3($db_path);

                $this->connection_success = $this->link instanceof SQLite3;

                if ($this->connection_success) {
                    $this->connection_type = "local";
                }
            }

        }

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

    private function requestData(string $sql): array|null {

        $result = $this->link->query($sql);

        $rows = [];
        if ($this->connection_type === "local") {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $rows[] = $row;
            }
        } else {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        if (!count($rows)) {
            return null;
        }
        
        return $rows;

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

        // Fill in optional parameters

        if ($query_params) {

            foreach ($query_params as $key => $value) {
                $sql = str_replace("{" . $key . "}", $value, $sql);
            }

        }

        $result = $this->requestData($sql);

        if ($result === null) {
            return null;
        }
        
        return $result;
        
    }

    public function connectionStatus() {
        return $this->connection_success;
    }

    public function getConnection(): mysqli|null {
        if ($this->connection_success) {
            return $this->link;
        }
        return null;
    }

}


?>