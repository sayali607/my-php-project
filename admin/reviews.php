```php
<?php

require_once "../config/db.php";
require_once "../includes/admin_check.php";

$admin_name = $_SESSION["admin_name"] ?? "Administrator";

$message = "";
$message_type = "";

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["delete_review"])
) {

    $review_id = isset($_POST["review_id"])
        ? (int)$_POST["review_id"]
        : 0;

    if ($review_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM reviews WHERE id = ?"
        );

        if ($stmt) {

            $stmt->bind_param(
                "i",
                $review_id
            );

            if ($stmt->execute()) {

                $message = "Review deleted successfully.";
                $message_type = "success";

            } else {

                $message = "Unable to delete review.";
                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message = "Unable to prepare delete query.";
            $message_type = "error";
        }
    }
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["toggle_public"])
) {

    $review_id = isset($_POST["review_id"])
        ? (int)$_POST["review_id"]
        : 0;

    if ($review_id > 0) {

        $stmt = $conn->prepare(
            "UPDATE reviews
             SET is_public =
                 CASE
                     WHEN is_public = 1 THEN 0
                     ELSE 1
                 END
             WHERE id = ?"
        );

        if ($stmt) {

            $stmt->bind_param(
                "i",
                $review_id
            );

            if ($stmt->execute()) {

                $message = "Review visibility updated.";
                $message_type = "success";

            } else {

                $message = "Unable to update review visibility.";
                $message_type = "error";
            }

            $stmt->close();

        } else {

            $message = "Unable to prepare update query.";
            $message_type = "error";
        }
    }
}

$review_query = "
    SELECT
        r.id,
        r.rating,
        r.review_text,
        r.experience_text,
        r.is_public,
        r.created_at,

        u.name AS user_name,

        tp.name AS place_name,
        tp.location AS place_location

    FROM reviews r

    INNER JOIN users u
        ON r.user_id = u.id

    INNER JOIN tourist_places tp
        ON r.place_id = tp.id

    ORDER BY r.created_at DESC
";

$reviews = $conn->query($review_query);

$total_reviews = 0;

$total_query = $conn->query(
    "SELECT COUNT(*) AS total
     FROM reviews"
);

if ($total_query) {

    $total_data = $total_query->fetch_assoc();

    $total_reviews = (int)$total_data["total"];
}

$public_reviews = 0;

$public_query = $conn->query(
    "SELECT COUNT(*) AS total
     FROM reviews
     WHERE is_public = 1"
);

if ($public_query) {

    $public_data = $public_query->fetch_assoc();

    $public_reviews = (int)$public_data["total"];
}

$hidden_reviews =
    $total_reviews - $public_reviews;

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
        Manage Reviews - ExploreWorld
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

            background:
                linear-gradient(
                    135deg,
                    #eef6ff,
                    #f8fafc
                );

            color: #1e293b;

            min-height: 100vh;

        }

        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 260px;

            height: 100vh;

            background:
                linear-gradient(
                    180deg,
                    #102a43,
                    #163b5c
                );

            padding: 28px 18px;

            box-shadow:
                5px 0 25px
                rgba(0, 0, 0, 0.08);

            z-index: 1000;

            overflow-y: auto;

        }

        .brand {

            display: block;

            text-align: center;

            text-decoration: none;

            color: white;

            font-size: 27px;

            font-weight: 800;

            margin-bottom: 7px;

        }

        .brand span {

            color: #60a5fa;

        }

        .brand-subtitle {

            text-align: center;

            color: #bfdbfe;

            font-size: 12px;

            margin-bottom: 32px;

            letter-spacing: 0.5px;

        }

        .admin-profile {

            background:
                rgba(255, 255, 255, 0.08);

            border:
                1px solid
                rgba(255, 255, 255, 0.10);

            border-radius: 16px;

            padding: 17px;

            margin-bottom: 28px;

        }

        .profile-icon {

            width: 48px;

            height: 48px;

            border-radius: 50%;

            background: #2563eb;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;

            margin-bottom: 11px;

        }

        .admin-profile h3 {

            color: white;

            font-size: 15px;

            margin-bottom: 5px;

            word-break: break-word;

        }

        .nav-title {

            color: #93c5fd;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: 1px;

            padding: 0 12px;

            margin-bottom: 10px;

        }

        .sidebar-nav a {

            display: flex;

            align-items: center;

            gap: 12px;

            color: #e2e8f0;

            text-decoration: none;

            padding: 13px 14px;

            margin-bottom: 7px;

            border-radius: 11px;

            font-size: 14px;

            font-weight: 600;

            transition: 0.25s ease;

        }

        .sidebar-nav a:hover {

            background:
                rgba(255, 255, 255, 0.10);

            color: white;

            transform: translateX(3px);

        }

        .sidebar-nav a.active {

            background: #2563eb;

            color: white;

            box-shadow:
                0 6px 15px
                rgba(37, 99, 235, 0.25);

        }

        .nav-icon {

            width: 25px;

            text-align: center;

            font-size: 18px;

        }

        .logout-link {

            margin-top: 28px !important;

            background:
                rgba(239, 68, 68, 0.90);

        }

        .logout-link:hover {

            background: #dc2626 !important;

        }

        .main {

            margin-left: 260px;

            min-height: 100vh;

            padding: 35px;

        }

        .top-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 30px;

        }

        .header-text h1 {

            font-size: 34px;

            color: #102a43;

            margin-bottom: 8px;

        }

        .header-text p {

            color: #64748b;

            line-height: 1.6;

        }

        .header-badge {

            background: white;

            border: 1px solid #e2e8f0;

            border-radius: 12px;

            padding: 11px 15px;

            color: #475569;

            font-size: 13px;

            font-weight: 600;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.05);

        }

        .page-banner {

            background:
                linear-gradient(
                    135deg,
                    #102a43,
                    #2563eb
                );

            color: white;

            border-radius: 22px;

            padding: 30px;

            margin-bottom: 30px;

            box-shadow:
                0 12px 35px
                rgba(37, 99, 235, 0.18);

        }

        .page-banner h2 {

            font-size: 25px;

            margin-bottom: 8px;

        }

        .page-banner p {

            color: #dbeafe;

            font-size: 14px;

        }

        .alert {

            padding: 15px 18px;

            border-radius: 12px;

            margin-bottom: 25px;

            font-weight: 600;

        }

        .success {

            background: #dcfce7;

            color: #166534;

            border:
                1px solid #86efac;

        }

        .error {

            background: #fee2e2;

            color: #991b1b;

            border:
                1px solid #fca5a5;

        }

        .stats {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;

        }

        .stat-card {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 18px;

            padding: 23px;

            box-shadow:
                0 8px 25px
                rgba(0,0,0,0.06);

        }

        .stat-icon {

            font-size: 25px;

            margin-bottom: 10px;

        }

        .stat-card h3 {

            color: #64748b;

            font-size: 14px;

            margin-bottom: 7px;

        }

        .stat-card p {

            font-size: 32px;

            font-weight: 800;

            color: #102a43;

        }

        .reviews-section {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 20px;

            padding: 25px;

            box-shadow:
                0 8px 28px
                rgba(0,0,0,0.06);

        }

        .section-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            margin-bottom: 22px;

        }

        .section-header h2 {

            color: #102a43;

            font-size: 23px;

        }

        .review-total {

            background: #eff6ff;

            color: #1d4ed8;

            padding: 8px 14px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 700;

        }

        .review-card {

            border:
                1px solid #e2e8f0;

            border-radius: 17px;

            padding: 23px;

            margin-bottom: 18px;

            background: #ffffff;

            transition: 0.25s ease;

        }

        .review-card:hover {

            box-shadow:
                0 8px 25px
                rgba(0,0,0,0.07);

            transform: translateY(-2px);

        }

        .review-top {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

            gap: 20px;

            margin-bottom: 15px;

        }

        .user-info {

            display: flex;

            align-items: center;

            gap: 12px;

        }

        .user-avatar {

            width: 48px;

            height: 48px;

            border-radius: 50%;

            background: #dbeafe;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;

            flex-shrink: 0;

        }

        .user-info h3 {

            color: #102a43;

            font-size: 17px;

            margin-bottom: 3px;

        }

        .user-info p {

            color: #64748b;

            font-size: 12px;

        }

        .status {

            padding: 7px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

        }

        .status-public {

            background: #dcfce7;

            color: #166534;

        }

        .status-hidden {

            background: #fee2e2;

            color: #991b1b;

        }

        .place-info {

            background: #f8fafc;

            border:
                1px solid #e2e8f0;

            padding: 13px 15px;

            border-radius: 11px;

            margin-bottom: 17px;

        }

        .place-info strong {

            color: #102a43;

        }

        .place-info span {

            color: #64748b;

            font-size: 13px;

        }

        .rating {

            color: #f59e0b;

            font-size: 19px;

            margin-bottom: 15px;

        }

        .review-content {

            margin-bottom: 18px;

        }

        .review-content h4 {

            color: #102a43;

            font-size: 14px;

            margin-bottom: 7px;

        }

        .review-content p {

            color: #475569;

            line-height: 1.7;

            white-space: pre-line;

            font-size: 14px;

        }

        .experience {

            background: #eff6ff;

            border-left:
                4px solid #2563eb;

            padding: 15px;

            border-radius: 10px;

            margin-top: 15px;

        }

        .experience h4 {

            color: #1d4ed8;

            margin-bottom: 7px;

            font-size: 14px;

        }

        .experience p {

            color: #334155;

            white-space: pre-line;

            line-height: 1.7;

            font-size: 14px;

        }

        .review-footer {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            padding-top: 17px;

            border-top:
                1px solid #e2e8f0;

        }

        .review-date {

            color: #94a3b8;

            font-size: 12px;

        }

        .actions {

            display: flex;

            gap: 8px;

            flex-wrap: wrap;

        }

        .action-btn {

            border: none;

            padding: 9px 13px;

            border-radius: 9px;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s;

        }

        .toggle-btn {

            background: #eff6ff;

            color: #1d4ed8;

        }

        .toggle-btn:hover {

            background: #dbeafe;

        }

        .delete-btn {

            background: #fee2e2;

            color: #b91c1c;

        }

        .delete-btn:hover {

            background: #fecaca;

        }

        .empty {

            text-align: center;

            padding: 60px 20px;

            color: #64748b;

        }

        .empty-icon {

            font-size: 55px;

            margin-bottom: 15px;

        }

        .empty h3 {

            color: #102a43;

            margin-bottom: 7px;

        }

        .footer {

            margin-top: 30px;

            padding: 22px 0;

            text-align: center;

            color: #64748b;

            font-size: 13px;

        }

        @media (max-width: 1000px) {

            .sidebar {

                width: 230px;

            }

            .main {

                margin-left: 230px;

            }

            .stats {

                grid-template-columns:
                    1fr 1fr;

            }

        }

        @media (max-width: 750px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

                padding: 22px 16px;

            }

            .main {

                margin-left: 0;

                padding: 25px 18px;

            }

            .top-header {

                flex-direction: column;

                align-items: flex-start;

            }

            .stats {

                grid-template-columns: 1fr;

            }

            .section-header {

                flex-direction: column;

                align-items: flex-start;

            }

            .review-top {

                flex-direction: column;

            }

            .review-footer {

                flex-direction: column;

                align-items: flex-start;

            }

            .actions {

                width: 100%;

            }

            .action-btn {

                flex: 1;

            }

        }

        @media (max-width: 450px) {

            .header-text h1 {

                font-size: 28px;

            }

            .page-banner {

                padding: 24px 20px;

            }

            .reviews-section {

                padding: 18px;

            }

            .review-card {

                padding: 18px;

            }

        }

    </style>

</head>

<body>

<aside class="sidebar">

    <a
        href="../index.php"
        class="brand"
    >

        Explore<span>World</span>

    </a>

    <div class="brand-subtitle">

        ADMINISTRATION PANEL

    </div>

    <div class="admin-profile">

        <div class="profile-icon">

            👤

        </div>

        <h3>

            <?php

            echo htmlspecialchars(
                $admin_name
            );

            ?>

        </h3>

    </div>

    <div class="nav-title">

        Main Menu

    </div>

    <nav class="sidebar-nav">

        <a href="dashboard.php">

            <span class="nav-icon">
                🏠
            </span>

            Dashboard

        </a>

        <a
            href="reviews.php"
            class="active"
        >

            <span class="nav-icon">
                ⭐
            </span>

            Reviews

        </a>

        <a href="../places/index.php">

            <span class="nav-icon">
                📍
            </span>

            Places

        </a>

        <a href="../index.php">

            <span class="nav-icon">
                🌍
            </span>

            View Website

        </a>

        <a
            href="logout.php"
            class="logout-link"
        >

            <span class="nav-icon">
                🚪
            </span>

            Logout

        </a>

    </nav>

</aside>

<main class="main">

    <div class="top-header">

        <div class="header-text">

            <h1>

                Manage Reviews

            </h1>

            <p>

                View, manage and control traveller reviews.

            </p>

        </div>

        <div class="header-badge">

            🔐 Secure Admin Area

        </div>

    </div>

    <?php if ($message !== ""): ?>

        <div
            class="alert <?php echo htmlspecialchars($message_type); ?>"
        >

            <?php

            echo htmlspecialchars(
                $message
            );

            ?>

        </div>

    <?php endif; ?>

    <section class="page-banner">

        <h2>

            ⭐ Traveller Reviews

        </h2>

        <p>

            Review user feedback and decide which reviews
            should be visible on the website.

        </p>

    </section>

    <div class="stats">

        <div class="stat-card">

            <div class="stat-icon">
                ⭐
            </div>

            <h3>
                Total Reviews
            </h3>

            <p>
                <?php echo $total_reviews; ?>
            </p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                👁️
            </div>

            <h3>
                Public Reviews
            </h3>

            <p>
                <?php echo $public_reviews; ?>
            </p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">
                🔒
            </div>

            <h3>
                Hidden Reviews
            </h3>

            <p>
                <?php echo $hidden_reviews; ?>
            </p>

        </div>

    </div>

    <section class="reviews-section">

        <div class="section-header">

            <h2>

                All Traveller Reviews

            </h2>

            <span class="review-total">

                <?php echo $total_reviews; ?>

                Reviews

            </span>

        </div>

        <?php if ($reviews && $reviews->num_rows > 0): ?>

            <?php while ($review = $reviews->fetch_assoc()): ?>

                <div class="review-card">

                    <div class="review-top">

                        <div class="user-info">

                            <div class="user-avatar">

                                👤

                            </div>

                            <div>

                                <h3>

                                    <?php

                                    echo htmlspecialchars(
                                        $review["user_name"]
                                    );

                                    ?>

                                </h3>

                                <p>

                                    Traveller

                                </p>

                            </div>

                        </div>

                        <?php if ((int)$review["is_public"] === 1): ?>

                            <span class="status status-public">

                                ● Public

                            </span>

                        <?php else: ?>

                            <span class="status status-hidden">

                                ● Hidden

                            </span>

                        <?php endif; ?>

                    </div>

                    <div class="place-info">

                        📍

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $review["place_name"]
                            );

                            ?>

                        </strong>

                        <?php if (!empty($review["place_location"])): ?>

                            <span>

                                —

                                <?php

                                echo htmlspecialchars(
                                    $review["place_location"]
                                );

                                ?>

                            </span>

                        <?php endif; ?>

                    </div>

                    <div class="rating">

                        <?php

                        $rating =
                            (int)$review["rating"];

                        if ($rating < 0) {

                            $rating = 0;

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

                    <?php if (!empty($review["review_text"])): ?>

                        <div class="review-content">

                            <h4>

                                💬 Review

                            </h4>

                            <p>

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $review["review_text"]
                                    )
                                );

                                ?>

                            </p>

                        </div>

                    <?php endif; ?>

                    <?php if (!empty($review["experience_text"])): ?>

                        <div class="experience">

                            <h4>

                                ✍️ Traveller Experience

                            </h4>

                            <p>

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $review["experience_text"]
                                    )
                                );

                                ?>

                            </p>

                        </div>

                    <?php endif; ?>

                    <div class="review-footer">

                        <div class="review-date">

                            📅

                            <?php

                            if (!empty($review["created_at"])) {

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $review["created_at"]
                                    )
                                );

                            } else {

                                echo "Date unavailable";

                            }

                            ?>

                        </div>

                        <div class="actions">

                            <form
                                method="POST"
                                onsubmit="
                                    return confirm(
                                        'Are you sure you want to change review visibility?'
                                    );
                                "
                            >

                                <input
                                    type="hidden"
                                    name="review_id"
                                    value="<?php echo (int)$review["id"]; ?>"
                                >

                                <button
                                    type="submit"
                                    name="toggle_public"
                                    class="action-btn toggle-btn"
                                >

                                    <?php

                                    if ((int)$review["is_public"] === 1) {

                                        echo "🔒 Hide Review";

                                    } else {

                                        echo "👁️ Make Public";

                                    }

                                    ?>

                                </button>

                            </form>

                            <form
                                method="POST"
                                onsubmit="
                                    return confirm(
                                        'Are you sure you want to permanently delete this review?'
                                    );
                                "
                            >

                                <input
                                    type="hidden"
                                    name="review_id"
                                    value="<?php echo (int)$review["id"]; ?>"
                                >

                                <button
                                    type="submit"
                                    name="delete_review"
                                    class="action-btn delete-btn"
                                >

                                    🗑️ Delete

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty">

                <div class="empty-icon">

                    ⭐

                </div>

                <h3>

                    No Reviews Yet

                </h3>

                <p>

                    Traveller reviews will appear here
                    when users submit them.

                </p>

            </div>

        <?php endif; ?>

    </section>

    <footer class="footer">

        © <?php echo date("Y"); ?>

        ExploreWorld —

        Discover. Explore. Experience.

    </footer>

</main>

</body>

</html>
```
