
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["user_id"])) {

    header("Location: /tourist_recommendation/auth/login.php");
    exit;
}

?>