<?php


require_once(__DIR__ . "/middleware.php");


class Login implements Middleware {

    private string $target;
    private string $fallback;
    private Database $db;

    public function __construct(string $target, string $fallback, Database $db) {
        $this->target = $target;
        $this->fallback = $fallback;
        $this->db = $db;
    }

    public function log(string $message): void {
        error_log(
            message: "middleware/login.php: '{$message}'"
        );
        return;
    }

    public function apply() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id();

        if (($resp = $this->control()) !== "") {
            $this->log("Failed to login: {$resp}");
            header("Location: " . $this->fallback . "?err=" . $resp);
            exit();
        } else {
            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id();

            $this->log("Logged in successfully");
            
            $_SESSION["login_state"] = [
                "type"=>"user",
                "email"=>$_POST["email"],
                "timestamp"=>time()
            ];

            header("Location: " . $this->target);
            exit();
        }
        
    }

    private function control(): string {

        // TODO: ensure that same account isn't logged into twice

        usleep(300000); // Brute force Delay

        if (
            empty($_POST["email"]) ||
            empty($_POST["password"])
        ) {
            $this->log("No login params provided");
            return "Please login";
        }

        if (!$this->db->connectionStatus()) {
            $this->log("Database connection failed");
            return "Database connection failed";
        }

        $email = $_POST["email"];
        $password = $_POST["password"];

        $result = $this->db->query(
            query: "accounts/getAccountByEmail",
            query_params: [["key"=>"email", "value"=>$email]]
        );

        if ($result === []) {
            $this->log("Account doesn't exist");
            return "Account doesn't exist";
        }

        if (!password_verify($password, $result[0]["password_hash"])) {
            $this->log("Incorrect password");
            return "Incorrect password";
        }

        return "";

    }

}


?>