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

    public function apply() {

        if (($res = $this->control()) === "") {

            session_start();
            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id();
            
            $_SESSION["login_state"] = [
                "type"=>"user",
                "email"=>$_POST["email"],
                "timestamp"=>time()
            ];

            header("Location: " . $this->target);
            
        } else {
            header("Location: " . $this->fallback . "?err=" . $res);
        }
        
    }

    private function control(): string {

        // TODO: ensure that same account isn't logged into twice

        usleep(300000); // Brute force Delay

        if (
            empty($_POST["email"]) ||
            empty($_POST["password"])
        ) {
            return "Please login";
        }

        if (!$this->db->connectionStatus()) {
            return "Database connection failed";
        }

        $email = $_POST["email"];
        $password = $_POST["password"];

        $result = $this->db->query(
            query: "accounts/getAccountByEmail",
            query_params: [["key"=>"email", "value"=>$email]]
        );

        if ($result === []) {
            return "Account doesn't exist";
        }

        if (!password_verify($password, $result[0]["password_hash"])) {
            return "Incorrect password";
        }

        return "";

    }

}




?>