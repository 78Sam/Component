<?php


require_once('vendor/autoload.php');


class Database {

    private $HOSTNAME;
    private $DATABASE;
    private $USERNAME;
    private $PASSWORD;
    private $link;

    function __construct() {
        
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $this->HOSTNAME = $_ENV["HOSTNAME"];
        $this->DATABASE = $_ENV["DATABASE"];
        $this->USERNAME = $_ENV["USERNAME"];
        $this->PASSWORD = $_ENV["PASSWORD"];

        $this->link = new mysqli($this->HOSTNAME, $this->USERNAME, $this->PASSWORD, $this->DATABASE);

    }

    public function getConnection(): mysqli {
        return $this->link;
    }


    public function getName() {
        return $this->HOSTNAME;
    }

}


?>