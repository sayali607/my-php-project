
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_user_logged_in =
    isset($_SESSION["user_id"]) &&
    !empty($_SESSION["user_id"]);

if (!$is_user_logged_in) {

    header(
        "Location: /tourist_recommendation/auth/login.php"
    );

    exit;
}

?>