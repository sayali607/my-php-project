```php
<?php

session_start();

require_once "../includes/auth_check.php";
require_once "../config/db.php";

$place_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($place_id <= 0) {
    header("Location: index.php");
    exit;
}

$is_user_logged_in =
    isset($_SESSION["user_id"]) &&
    !empty($_SESSION["user_id"]);

$is_admin_logged_in =
    isset($_SESSION["admin_id"]) &&
    !empty($_SESSION["admin_id"]) &&
    isset($_SESSION["admin_role"]) &&
    $_SESSION["admin_role"] === "admin";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!$is_user_logged_in) {

        $message =
            "Please login as a user to submit a review.";

        $message_type = "error";

    } else {

        $user_id = (int)$_SESSION["user_id"];

        $rating = isset($_POST["rating"])
            ? (int)$_POST["rating"]
            : 0;

        $review_text =
            trim($_POST["review_text"] ?? "");

        $experience_text =
            trim($_POST["experience_text"] ?? "");

        if ($rating < 1 || $rating > 5) {

            $message =
                "Please select a rating between 1 and 5.";

            $message_type = "error";

        } elseif (
            $review_text === "" &&
            $experience_text === ""
        ) {

            $message =
                "Please write a review or share your experience.";

            $message_type = "error";

        } else {

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

            if ($stmt) {

                $stmt->bind_param(
                    "iiiss",
                    $user_id,
                    $place_id,
                    $rating,
                    $review_text,
                    $experience_text
                );

                if ($stmt->execute()) {

                    $message =
                        "Your review and experience were submitted successfully!";

                    $message_type = "success";

                } else {

                    $message =
                        "Something went wrong while saving your review.";

                    $message_type = "error";
                }

                $stmt->close();

            } else {

                $message =
                    "Unable to prepare the review.";

                $message_type = "error";
            }
        }
    }
}

$stmt = $conn->prepare(
    "SELECT
        id,
        name,
        location,
        category,
        budget,
        description,
        best_time,
        activities,
        image,
        map_link,
        created_at
     FROM tourist_places
     WHERE id = ?
     LIMIT 1"
);

if (!$stmt) {
    die("Database query failed.");
}

$stmt->bind_param(
    "i",
    $place_id
);

$stmt->execute();

$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {

    $stmt->close();

    header("Location: index.php");
    exit;
}

$place = $result->fetch_assoc();

$stmt->close();

$image_name = trim($place["image"] ?? "");

if ($image_name !== "") {

    $image_url =
        "../uploads/places/" .
        rawurlencode($image_name);

} else {

    $image_url =
        "../assets/images/hero.jpg";
}

$review_stmt = $conn->prepare(
    "SELECT
        r.id,
        r.rating,
        r.review_text,
        r.experience_text,
        r.created_at,
        u.name AS user_name
     FROM reviews r
     INNER JOIN users u
        ON r.user_id = u.id
     WHERE
        r.place_id = ?
        AND r.is_public = 1
     ORDER BY r.created_at DESC"
);

$reviews = false;

if ($review_stmt) {

    $review_stmt->bind_param(
        "i",
        $place_id
    );

    $review_stmt->execute();

    $reviews = $review_stmt->get_result();
}

$total_reviews = 0;

$count_stmt = $conn->prepare(
    "SELECT
        COUNT(*) AS total_reviews
     FROM reviews
     WHERE
        place_id = ?
        AND is_public = 1"
);

if ($count_stmt) {

    $count_stmt->bind_param(
        "i",
        $place_id
    );

    $count_stmt->execute();

    $count_result =
        $count_stmt->get_result();

    if ($count_result) {

        $count_data =
            $count_result->fetch_assoc();

        $total_reviews =
            (int)$count_data["total_reviews"];
    }

    $count_stmt->close();
}

$destination =
    $place["name"] .
    ", " .
    $place["location"];

$directions_url =
    "https://www.google.com/maps/dir/?api=1&destination=" .
    urlencode($destination);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        <?php
        echo htmlspecialchars($place["name"]);
        ?>

        - ExploreWorld

    </title>

    <style>

        * {

            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f7fb;

            color: #1f2937;

            line-height: 1.6;

        }

        .navbar {

            background:
                linear-gradient(
                    135deg,
                    #082337,
                    #163b5c
                );

            padding: 17px 7%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            position: sticky;

            top: 0;

            z-index: 1000;

            box-shadow:
                0 4px 20px
                rgba(0,0,0,0.15);

        }

        .logo {

            color: white;

            text-decoration: none;

            font-size: 27px;

            font-weight: 800;

        }

        .logo span {

            color: #20c997;

        }

        .nav-links {

            display: flex;

            align-items: center;

            gap: 25px;

        }

        .nav-links a {

            color: white;

            text-decoration: none;

            font-weight: 600;

            transition: 0.3s;

        }

        .nav-links a:hover {

            color: #20c997;

        }

        .container {

            width: 88%;

            max-width: 1200px;

            margin: auto;

            padding: 45px 0 70px;

        }

        .alert {

            padding: 16px 20px;

            border-radius: 12px;

            margin-bottom: 25px;

            font-weight: 600;

        }

        .success {

            background: #dcfce7;

            color: #166534;

            border: 1px solid #86efac;

        }

        .error {

            background: #fee2e2;

            color: #991b1b;

            border: 1px solid #fca5a5;

        }

        .place-card {

            background: white;

            border-radius: 22px;

            overflow: hidden;

            box-shadow:
                0 12px 40px
                rgba(0,0,0,0.09);

        }

        .place-image {

            width: 100%;

            height: 480px;

            object-fit: cover;

            display: block;

            background: #dfe7eb;

        }

        .place-content {

            padding: 40px;

        }

        .place-title {

            font-size: 43px;

            line-height: 1.2;

            color: #082337;

            margin-bottom: 12px;

        }

        .location {

            color: #2563eb;

            font-weight: 700;

            font-size: 17px;

            margin-bottom: 22px;

        }

        .badges {

            display: flex;

            flex-wrap: wrap;

            gap: 10px;

            margin-bottom: 30px;

        }

        .badge {

            padding: 9px 15px;

            border-radius: 30px;

            background: #eff6ff;

            color: #1d4ed8;

            font-size: 14px;

            font-weight: 700;

        }

        .info-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

            margin: 30px 0;

        }

        .info-box {

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            padding: 23px;

            border-radius: 15px;

        }

        .info-box h3 {

            margin-bottom: 9px;

            color: #102a43;

        }

        .info-box p {

            color: #64748b;

            white-space: pre-line;

        }

        .description-section {

            margin-top: 30px;

        }

        .description-section h2 {

            margin-bottom: 12px;

            color: #102a43;

        }

        .description {

            color: #4b5563;

            line-height: 1.9;

        }

        .buttons {

            display: flex;

            flex-wrap: wrap;

            gap: 12px;

            margin-top: 30px;

        }

        .btn {

            display: inline-block;

            padding: 14px 22px;

            border-radius: 11px;

            text-decoration: none;

            font-weight: 700;

            transition: 0.25s;

        }

        .map-btn {

            background: #2563eb;

            color: white;

        }

        .map-btn:hover {

            background: #1d4ed8;

            transform: translateY(-2px);

        }

        .direction-btn {

            background: #16a34a;

            color: white;

        }

        .direction-btn:hover {

            background: #15803d;

            transform: translateY(-2px);

        }

        .back-btn {

            background: #e5e7eb;

            color: #111827;

        }

        .back-btn:hover {

            background: #d1d5db;

        }

        .reviews-section {

            margin-top: 45px;

        }

        .section-heading {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 22px;

            gap: 15px;

        }

        .section-heading h2 {

            font-size: 30px;

            color: #102a43;

        }

        .review-count {

            background: #dbeafe;

            color: #1d4ed8;

            padding: 8px 14px;

            border-radius: 20px;

            font-weight: 700;

            white-space: nowrap;

        }

        .review-card {

            background: white;

            padding: 25px;

            border-radius: 17px;

            margin-bottom: 18px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.06);

        }

        .review-header {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 15px;

            margin-bottom: 10px;

        }

        .user-name {

            font-size: 18px;

            font-weight: 700;

            color: #102a43;

        }

        .review-date {

            color: #94a3b8;

            font-size: 13px;

        }

        .stars {

            margin: 8px 0 15px;

            font-size: 18px;

        }

        .review-text {

            color: #4b5563;

            margin-bottom: 15px;

            white-space: pre-line;

        }

        .experience-box {

            background: #eff6ff;

            border-left: 4px solid #2563eb;

            padding: 18px;

            border-radius: 10px;

            color: #334155;

        }

        .experience-box strong {

            display: block;

            margin-bottom: 8px;

            color: #1d4ed8;

        }

        .no-reviews {

            background: white;

            padding: 35px;

            text-align: center;

            border-radius: 16px;

            color: #64748b;

        }

        .write-review {

            margin-top: 40px;

            background: white;

            padding: 32px;

            border-radius: 20px;

            box-shadow:
                0 8px 25px
                rgba(0,0,0,0.07);

        }

        .write-review h2 {

            margin-bottom: 8px;

            color: #102a43;

        }

        .write-review-subtitle {

            color: #64748b;

            margin-bottom: 25px;

        }

        .form-group {

            margin-bottom: 20px;

        }

        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: 700;

        }

        .rating-options {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

        }

        .rating-options label {

            cursor: pointer;

            padding: 9px 13px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            border-radius: 9px;

            transition: 0.2s;

        }

        .rating-options label:hover {

            background: #eff6ff;

            border-color: #93c5fd;

        }

        .rating-options input {

            margin-right: 5px;

        }

        textarea {

            width: 100%;

            min-height: 130px;

            resize: vertical;

            padding: 14px;

            border: 1px solid #cbd5e1;

            border-radius: 11px;

            font-family: inherit;

            font-size: 15px;

            outline: none;

        }

        textarea:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37,99,235,0.10);

        }

        .submit-btn {

            border: none;

            background: #2563eb;

            color: white;

            padding: 14px 25px;

            border-radius: 11px;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.25s;

        }

        .submit-btn:hover {

            background: #1d4ed8;

            transform: translateY(-2px);

        }

        .login-notice {

            background: #eff6ff;

            color: #1e40af;

            padding: 18px;

            border-radius: 12px;

            margin-top: 20px;

        }

        .login-notice a {

            color: #1d4ed8;

            font-weight: 700;

        }

        .footer {

            background: #061923;

            color: white;

            text-align: center;

            padding: 30px;

        }

        .footer-logo {

            font-size: 20px;

            font-weight: bold;

            margin-bottom: 5px;

        }

        .footer-logo span {

            color: #20c997;

        }

        .footer p {

            color: #bbb;

            font-size: 13px;

        }

        @media (max-width: 800px) {

            .navbar {

                flex-direction: column;

                gap: 15px;

            }

            .place-image {

                height: 300px;

            }

            .place-content {

                padding: 25px;

            }

            .place-title {

                font-size: 32px;

            }

            .info-grid {

                grid-template-columns: 1fr;

            }

            .section-heading {

                flex-direction: column;

                align-items: flex-start;

            }

        }

        @media (max-width: 550px) {

            .container {

                width: 92%;

            }

            .place-image {

                height: 240px;

            }

            .place-title {

                font-size: 28px;

            }

            .buttons {

                flex-direction: column;

            }

            .btn {

                text-align: center;

                width: 100%;

            }

            .review-header {

                flex-direction: column;

            }

            .navbar {

                padding: 15px 5%;

            }

            .nav-links {

                gap: 15px;

            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <a
        href="../index.php"
        class="logo"
    >

        Explore<span>World</span>

    </a>

    <div class="nav-links">

        <a href="../index.php">

            🏠 Home

        </a>

        <a href="index.php">

            🌍 Places

        </a>

    </div>

</nav>

<main class="container">

    <?php if ($message !== ""): ?>

        <div class="alert <?php echo htmlspecialchars($message_type); ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>

    <div class="place-card">

        <img
            src="<?php echo htmlspecialchars($image_url); ?>"
            alt="<?php echo htmlspecialchars($place["name"]); ?>"
            class="place-image"
            onerror="this.onerror=null;this.src='../assets/images/hero.jpg';"
        >

        <div class="place-content">

            <h1 class="place-title">

                <?php
                echo htmlspecialchars(
                    $place["name"]
                );
                ?>

            </h1>

            <div class="location">

                📍

                <?php
                echo htmlspecialchars(
                    $place["location"]
                );
                ?>

            </div>

            <div class="badges">

                <?php if (!empty($place["category"])): ?>

                    <span class="badge">

                        🏷️

                        <?php
                        echo htmlspecialchars(
                            $place["category"]
                        );
                        ?>

                    </span>

                <?php endif; ?>

                <?php if (!empty($place["budget"])): ?>

                    <span class="badge">

                        💰

                        <?php
                        echo htmlspecialchars(
                            $place["budget"]
                        );
                        ?>

                    </span>

                <?php endif; ?>

                <span class="badge">

                    ⭐

                    <?php echo $total_reviews; ?>

                    Reviews

                </span>

            </div>

            <div class="info-grid">

                <div class="info-box">

                    <h3>

                        🕐 Best Time to Visit

                    </h3>

                    <p>

                        <?php

                        if (!empty($place["best_time"])) {

                            echo nl2br(
                                htmlspecialchars(
                                    $place["best_time"]
                                )
                            );

                        } else {

                            echo "Information not available.";

                        }

                        ?>

                    </p>

                </div>

                <div class="info-box">

                    <h3>

                        🎯 Activities

                    </h3>

                    <p>

                        <?php

                        if (!empty($place["activities"])) {

                            echo nl2br(
                                htmlspecialchars(
                                    $place["activities"]
                                )
                            );

                        } else {

                            echo "Information not available.";

                        }

                        ?>

                    </p>

                </div>

            </div>

            <div class="description-section">

                <h2>

                    About This Place

                </h2>

                <p class="description">

                    <?php

                    if (!empty($place["description"])) {

                        echo nl2br(
                            htmlspecialchars(
                                $place["description"]
                            )
                        );

                    } else {

                        echo "No description available.";

                    }

                    ?>

                </p>

            </div>

            <div class="buttons">

                <?php if (!empty($place["map_link"])): ?>

                    <a
                        href="<?php echo htmlspecialchars($place["map_link"]); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn map-btn"
                    >

                        🗺️ Open Map

                    </a>

                <?php endif; ?>

                <a
                    href="<?php echo htmlspecialchars($directions_url); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn direction-btn"
                >

                    🚗 Start Directions

                </a>

                <a
                    href="index.php"
                    class="btn back-btn"
                >

                    ← Back to Places

                </a>

            </div>

        </div>

    </div>

    <section class="reviews-section">

        <div class="section-heading">

            <h2>

                ⭐ Traveller Reviews

            </h2>

            <span class="review-count">

                <?php echo $total_reviews; ?>

                Public Reviews

            </span>

        </div>

        <?php if ($reviews && $reviews->num_rows > 0): ?>

            <?php while ($review = $reviews->fetch_assoc()): ?>

                <div class="review-card">

                    <div class="review-header">

                        <div>

                            <div class="user-name">

                                👤

                                <?php
                                echo htmlspecialchars(
                                    $review["user_name"]
                                );
                                ?>

                            </div>

                            <div class="stars">

                                <?php

                                $rating =
                                    (int)$review["rating"];

                                if ($rating < 1) {
                                    $rating = 1;
                                }

                                if ($rating > 5) {
                                    $rating = 5;
                                }

                                echo str_repeat(
                                    "⭐",
                                    $rating
                                );

                                echo str_repeat(
                                    "☆",
                                    5 - $rating
                                );

                                ?>

                            </div>

                        </div>

                        <div class="review-date">

                            <?php

                            if (!empty($review["created_at"])) {

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $review["created_at"]
                                    )
                                );

                            }

                            ?>

                        </div>

                    </div>

                    <?php if (!empty($review["review_text"])): ?>

                        <p class="review-text">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $review["review_text"]
                                )
                            );

                            ?>

                        </p>

                    <?php endif; ?>

                    <?php if (!empty($review["experience_text"])): ?>

                        <div class="experience-box">

                            <strong>

                                ✍️ Traveller Experience

                            </strong>

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $review["experience_text"]
                                )
                            );

                            ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-reviews">

                <h3>

                    No public reviews yet.

                </h3>

                <p>

                    Be the first traveller
                    to share your experience!

                </p>

            </div>

        <?php endif; ?>

    </section>

    <?php if ($is_user_logged_in): ?>

        <section class="write-review">

            <h2>

                ✍️ Write Your Review & Experience

            </h2>

            <p class="write-review-subtitle">

                Tell other travellers what you thought about this place.

            </p>

            <form
                method="POST"
                action="details.php?id=<?php echo $place_id; ?>"
            >

                <div class="form-group">

                    <label>

                        Your Rating

                    </label>

                    <div class="rating-options">

                        <label>

                            <input
                                type="radio"
                                name="rating"
                                value="1"
                                required
                            >

                            ⭐ 1

                        </label>

                        <label>

                            <input
                                type="radio"
                                name="rating"
                                value="2"
                            >

                            ⭐⭐ 2

                        </label>

                        <label>

                            <input
                                type="radio"
                                name="rating"
                                value="3"
                            >

                            ⭐⭐⭐ 3

                        </label>

                        <label>

                            <input
                                type="radio"
                                name="rating"
                                value="4"
                            >

                            ⭐⭐⭐⭐ 4

                        </label>

                        <label>

                            <input
                                type="radio"
                                name="rating"
                                value="5"
                            >

                            ⭐⭐⭐⭐⭐ 5

                        </label>

                    </div>

                </div>

                <div class="form-group">

                    <label for="review_text">

                        Your Review

                    </label>

                    <textarea
                        id="review_text"
                        name="review_text"
                        placeholder="What did you think about this place?"
                    ></textarea>

                </div>

                <div class="form-group">

                    <label for="experience_text">

                        Write Your Experience

                    </label>

                    <textarea
                        id="experience_text"
                        name="experience_text"
                        placeholder="Share your experience so other travellers can know what to expect..."
                    ></textarea>

                </div>

                <button
                    type="submit"
                    class="submit-btn"
                >

                    🚀 Submit Review & Experience

                </button>

            </form>

        </section>

    <?php else: ?>

        <section class="write-review">

            <h2>

                ✍️ Write Your Review & Experience

            </h2>

            <div class="login-notice">

                🔐 Please

                <a href="../auth/login.php">

                    login

                </a>

                as a user to write a review
                or share your experience.

            </div>

        </section>

    <?php endif; ?>

</main>

<footer class="footer">

    <div class="footer-logo">

        Explore<span>World</span>

    </div>

    <p>

        Discover places.
        Share experiences.
        Explore the world.

    </p>

</footer>

</body>

</html>
```
