```php
<?php

require_once "../includes/auth_check.php";
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];

$place_id = (int) ($_POST["place_id"] ?? 0);

$rating = (int) ($_POST["rating"] ?? 0);

$review_text = trim(
    $_POST["review_text"] ?? ""
);

$experience_text = trim(
    $_POST["experience_text"] ?? ""
);

if (
    $place_id <= 0 ||
    $rating < 1 ||
    $rating > 5 ||
    $review_text === ""
) {

    die("Invalid review information.");
}

$place_check = $conn->prepare(
    "SELECT id
     FROM tourist_places
     WHERE id = ?
     LIMIT 1"
);

if (!$place_check) {

    die("Unable to check tourist place.");
}

$place_check->bind_param(
    "i",
    $place_id
);

$place_check->execute();

$place_result = $place_check->get_result();

if ($place_result->num_rows !== 1) {

    $place_check->close();

    die("Tourist place not found.");
}

$place_check->close();

$stmt = $conn->prepare(
    "INSERT INTO reviews
    (
        user_id,
        place_id,
        rating,
        review_text,
        experience_text,
        is_public
    )
    VALUES (?, ?, ?, ?, ?, 1)"
);

if (!$stmt) {

    die("Unable to prepare review submission.");
}

$stmt->bind_param(
    "iiiss",
    $user_id,
    $place_id,
    $rating,
    $review_text,
    $experience_text
);

if ($stmt->execute()) {

    $stmt->close();

    header(
        "Location: details.php?id="
        . $place_id
        . "&review=success"
    );

    exit;
}

$stmt->close();

die(
    "Unable to submit review. Please try again."
);

?>
```
