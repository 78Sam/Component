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

        $files = scandir($this->ROOT_DIR . "/data");
        $this->data_points = [];

        // foreach ($files as $file) {
        //     $file_name = substr($file, 0, -5);
        //     $this->data_points[$file_name] = 0;
        // }

        $this->link = new mysqli($this->HOSTNAME, $this->USERNAME, $this->PASSWORD, $this->DATABASE);

    }

    function __destruct() {
        $this->link->close();
    }


    private function requestData(string $query, array|null $query_params) {

        $sql = file_get_contents($this->ROOT_DIR . "/data/" . $query . ".sql");

        if ($query_params) {

            foreach ($query_params as $key => $value) {
                $sql = str_replace("{" . $key . "}", $value, $sql);
            }

        }

        $result = $this->link->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        
        $this->data_points[$query] = $rows;

        return $rows;
    }


    public function getData(string $query, array|null $query_params) {
        if (array_key_exists($query, $this->data_points)) {
            return $this->data_points[$query];
        } else {
            return $this->requestData($query, $query_params);
        }
    }

}


?>