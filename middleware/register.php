<?php


require_once(__DIR__ . "/middleware.php");


class Register implements Middleware {

    private string $fallback;
    private Database $db;

    public function __construct(string $fallback, Database $db) {
        $this->fallback = $fallback;
        $this->db = $db;
    }

    public function apply() {

        if (($res = $this->control()) !== "") {
            header("Location: " . $this->fallback . "?err=" . $res);
        }
        
    }

    private function control(): string {

        if (
            empty($_POST["first_name"]) ||
            empty($_POST["surname"]) ||
            empty($_POST["email"]) ||
            empty($_POST["password"])
        ) {
            return "Please register";
        }

        if (!$this->db->connectionStatus()) {
            return "Database connection failed";
        }

        $first_name = $_POST["first_name"];
        $surname = $_POST["surname"];
        $full_name = "{$first_name} {$surname}";
        $email = $_POST["email"];
        $password = $_POST["password"];

        $type = "user";
        $created = date("H:i d/m/y");

        $result = $this->db->query(
            query: "accounts/getAccountByEmail",
            query_params: [
                ["key"=>"email", "value"=>$email]
            ]
        );

        if ($result !== []) {
            return "Account already exists with that email";
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $result = $this->db->query(
            query: "accounts/register",
            query_params: [
                ["key"=>"first_name", "value"=>$first_name],
                ["key"=>"surname", "value"=>$surname],
                ["key"=>"full_name", "value"=>$full_name],
                ["key"=>"email", "value"=>$email],
                ["key"=>"password_hash", "value"=>$password_hash],
                ["key"=>"type", "value"=>$type],
                ["key"=>"created", "value"=>$created]
            ]
        );

        return "";

    }

}




?>