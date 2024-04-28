<?php


require_once('vendor/autoload.php');


enum DatabaseType {
    case MariaDB;
    case SQLite3;

}


class Database {

    private $ROOT_DIR;
    private $link;
    private $db_type;
    private $connection_success;
    private $connection_type;

    function __construct() {
        
        $this->ROOT_DIR = __DIR__;
        // $this->connection_success = false;

        if (file_exists($this->ROOT_DIR . "/.env")) {

            $dotenv = Dotenv\Dotenv::createImmutable($this->ROOT_DIR);
            $dotenv->load();

            $this->connection_success = false;

            $local_hosts = ["127.0.0.1", "::1"];
            $is_local_host = in_array($_SERVER["REMOTE_ADDR"], $local_hosts);

            if (
                !$is_local_host &&
                isset($_ENV["HOSTNAME"]) && $_ENV["HOSTNAME"] &&
                isset($_ENV["DATABASE"]) && $_ENV["DATABASE"] &&
                isset($_ENV["USERNAME"]) && $_ENV["USERNAME"] &&
                isset($_ENV["PASSWORD"]) && $_ENV["PASSWORD"]
            ) {

                $HOSTNAME = $_ENV["HOSTNAME"];
                $DATABASE = $_ENV["DATABASE"];
                $USERNAME = $_ENV["USERNAME"];
                $PASSWORD = $_ENV["PASSWORD"];

                $this->link = new mysqli($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

                $this->connection_success = $this->link instanceof mysqli && $this->link->connect_error === null;

                if ($this->connection_success) {
                    $this->connection_type = "remote";
                }
            }

            if (
                !$this->connection_success && 
                isset($_ENV["LOCAL_TYPE"]) && $_ENV["LOCAL_TYPE"] &&
                isset($_ENV["LOCAL_DB"]) && $_ENV["LOCAL_DB"]
            ) {

                $db_path = $this->ROOT_DIR . "/data/" . $_ENV["LOCAL_DB"];

                $this->link = new SQLite3($db_path);

                $this->connection_success = $this->link instanceof SQLite3;

                if ($this->connection_success) {
                    $this->connection_type = "local";
                }
            }

        }

    }

    function __destruct() {

        if ($this->link) {
            $this->link->close();
        }

    }


    private function requestData(string $sql) {

        $result = $this->link->query($sql);

        if ($this->connection_type == "local") {
            $rows = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $rows[] = $row;
            }
        } else {
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        return $rows;
    }

    /**
     * 
     * 
     * Remember this returns an array of arrays, if you are expecting one result use $result[0]
     * 
     */
    public function query(string $query, array $query_params = null) {

        $sql = file_get_contents($this->ROOT_DIR . "/data/" . $query . ".sql");

        // Fill in optional parameters

        if ($query_params) {

            foreach ($query_params as $key => $value) {
                $sql = str_replace("{" . $key . "}", $value, $sql);
            }

        }

        // Check if query already taken place
        return $this->requestData($sql);
        
    }

    public function connectionStatus() {
        return $this->connection_success;
    }

}


?>