<?php


require_once(__DIR__ . "/middleware.php");


class Register implements Middleware {

    private string $target;
    private string $fallback;
    private Database $db;

    public function __construct(string $target, string $fallback, Database $db) {
        $this->target = $target;
        $this->fallback = $fallback;
        $this->db = $db;
    }

    public function apply() {

        if (($res = $this->control()) !== "") {
            header("Location: " . $this->fallback . "?err=" . $res);
        }
        
    }

    private function control(): string {

        if (!isset($_POST["email"], $_POST["password"]) || !$_POST["email"] || !$_POST["password"]) {
            return "";
        }

        if (!$this->db->connectionStatus()) {
            return "Database connection failed";
        }

        $email = $_POST["email"];
        $password = $_POST["password"];

        $result = $this->db->query(
            query: "login",
            query_params: [["key"=>"email", "value"=>$email]]
        );

        if ($result !== []) {
            return "Account already exists";
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $this->db->query(
            query: "register",
            query_params: [["key"=>"email", "value"=>$email], ["key"=>"password_hash", "value"=>$password_hash]]
        );

        return "";

    }

}




?>