<?php


require_once(__DIR__ . "/middleware.php");


class Login implements Middleware {

    private string $target;
    private string $fallback;
    private Database $db;
    private bool $starting;

    public function __construct(string $target, string $fallback, Database $db, bool $starting=true) {
        $this->target = $target;
        $this->fallback = $fallback;
        $this->db = $db;
        $this->starting = $starting;
    }

    public function isStarting(): bool {
        return $this->starting;
    }

    public function apply() {

        if (($res = $this->control()) !== "") {
            header("Location: " . $this->fallback . "?err=" . $res);
            exit();
        }

        session_start();
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id();
        
        $uid = bin2hex(random_bytes(128));
        $_SESSION["login_state"] = [
            "uid"=>$uid,
            "role"=>"admin",
            "timestamp"=>time()
        ];

    }

    private function control(): string {

        if (!isset($_POST["email"], $_POST["password"]) || !$_POST["email"] || !$_POST["password"]) {
            return "Please enter email and password";
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

        if ($result === []) {
            return "Account doesn't exist";
        }

        if (!password_verify($password, $result[0]["password_hash"])) {
            return "Invalid password";
        }

        return "";

    }

}




?>