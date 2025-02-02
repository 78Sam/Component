<?php


require_once(__DIR__ . "/middleware.php");


class Register implements Middleware {

    private string $fallback;
    private Database $db;

    public function __construct(string $fallback, Database $db) {
        $this->fallback = $fallback;
        $this->db = $db;
    }

    public function log(string $message): void {
        error_log(
            message: "middleware/register.php: '{$message}'"
        );
        return;
    }

    public function apply(): void {

        if (($res = $this->control()) !== "") {
            header("Location: " . $this->fallback . "?err=" . $res);
            exit();
        }

        return;
    }

    private function control(): string {

        if (
            empty($_POST["email"]) ||
            empty($_POST["password"])
        ) {
            return "";
        }

        if (!$this->db->connectionStatus()) {
            $this->log("DB connection failed");
            return "Database connection failed";
        }

        $email = $_POST["email"];
        $password = $_POST["password"];
        $created = date("H:i d/m/y");

        $result = $this->db->query(
            query: "accounts/getAccountByEmail",
            query_params: [
                ["key"=>"email", "value"=>$email]
            ]
        );

        if ($result !== []) {
            $this->log("Account already exists with that email: {$email}");
            return "Account already exists with that email";
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $result = $this->db->query(
            query: "accounts/register",
            query_params: [
                ["key"=>"email", "value"=>$email],
                ["key"=>"password_hash", "value"=>$password_hash],
                ["key"=>"created", "value"=>$created]
            ]
        );

        $this->log("Registration successful");

        return "";

    }

}


?>