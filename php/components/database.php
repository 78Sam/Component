<?php


require_once('vendor/autoload.php');


class Database {

    private $ROOT_DIR;
    private $HOSTNAME;
    private $DATABASE;
    private $USERNAME;
    private $PASSWORD;
    private $link;
    private $data_points;

    function __construct() {
        
        $this->ROOT_DIR = __DIR__;

        $dotenv = Dotenv\Dotenv::createImmutable($this->ROOT_DIR);
        $dotenv->load();

        $this->HOSTNAME = $_ENV["HOSTNAME"];
        $this->DATABASE = $_ENV["DATABASE"];
        $this->USERNAME = $_ENV["USERNAME"];
        $this->PASSWORD = $_ENV["PASSWORD"];

        $this->data_points = [];

        $this->link = new mysqli($this->HOSTNAME, $this->USERNAME, $this->PASSWORD, $this->DATABASE);
        // $this->link = new SQLite3($this->ROOT_DIR . "/data/testdb.db");

    }

    function __destruct() {
        $this->link->close();
    }


    private function requestData(string $sql) {

        $result = $this->link->query($sql);

        $rows = [];
        while ($row = $result->fetchArray()) {
            $rows[] = $row;
        }
        
        $this->data_points[$sql] = $rows;

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

        if (array_key_exists($sql, $this->data_points)) {
            return $this->data_points[$query];
        } else {
            return $this->requestData($sql);
        }
        
    }

}


?>