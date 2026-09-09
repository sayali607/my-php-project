```php
<?php

require_once "../config/db.php";
require_once "../includes/admin_check.php";

$admin_name = $_SESSION["admin_name"] ?? "Administrator";
$admin_email = $_SESSION["admin_email"] ?? "";

$review_query = $conn->query(
    "SELECT COUNT(*) AS total_reviews
     FROM reviews"
);

$review_data = $review_query->fetch_assoc();

$total_reviews = $review_data["total_reviews"];

$experience_query = $conn->query(
    "SELECT COUNT(*) AS total_experiences
     FROM reviews
     WHERE experience_text IS NOT NULL
     AND experience_text != ''"
);

$experience_data = $experience_query->fetch_assoc();

$total_experiences = $experience_data["total_experiences"];

$user_query = $conn->query(
    "SELECT COUNT(*) AS total_users
     FROM users"
);

$user_data = $user_query->fetch_assoc();

$total_users = $user_data["total_users"];

$place_query = $conn->query(
    "SELECT COUNT(*) AS total_places
     FROM tourist_places"
);

$place_data = $place_query->fetch_assoc();

$total_places = $place_data["total_places"];

$places_query = $conn->query(
    "SELECT
        id,
        name,
        location,
        category,
        budget,
        image
     FROM tourist_places
     ORDER BY id DESC"
);

$success_message = "";

$error_message = "";

if (isset($_GET["success"])) {

    if ($_GET["success"] === "deleted") {

        $success_message =
            "Tourist place deleted successfully.";

    }

}

if (isset($_GET["error"])) {

    if ($_GET["error"] === "not_found") {

        $error_message =
            "Tourist place was not found.";

    }

    if ($_GET["error"] === "delete_failed") {

        $error_message =
            "Unable to delete the tourist place.";

    }

    if ($_GET["error"] === "invalid_id") {

        $error_message =
            "Invalid place ID.";

    }

}

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
        Admin Dashboard - ExploreWorld
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

        .admin-profile p {

            color: #bfdbfe;

            font-size: 12px;

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

            white-space: nowrap;

        }

        .welcome-banner {

            position: relative;

            overflow: hidden;

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

        .welcome-banner::after {

            content: "🌍";

            position: absolute;

            right: 40px;

            top: 10px;

            font-size: 100px;

            opacity: 0.10;

            transform: rotate(-10deg);

        }

        .welcome-banner h2 {

            font-size: 24px;

            margin-bottom: 8px;

            position: relative;

            z-index: 1;

        }

        .welcome-banner p {

            color: #dbeafe;

            font-size: 14px;

            position: relative;

            z-index: 1;

        }

        .section-heading {

            color: #102a43;

            font-size: 23px;

            margin-bottom: 18px;

        }

        .cards {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 22px;

        }

        .dashboard-card {

            background: white;

            border:
                1px solid
                #e5e7eb;

            border-radius: 19px;

            padding: 25px;

            box-shadow:
                0 8px 28px
                rgba(0, 0, 0, 0.06);

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease;

        }

        .dashboard-card:hover {

            transform: translateY(-5px);

            box-shadow:
                0 15px 35px
                rgba(0, 0, 0, 0.10);

        }

        .dashboard-icon {

            width: 55px;

            height: 55px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eff6ff;

            font-size: 25px;

            margin-bottom: 18px;

        }

        .dashboard-card h3 {

            color: #475569;

            font-size: 15px;

            margin-bottom: 8px;

        }

        .dashboard-card p {

            color: #102a43;

            font-size: 36px;

            font-weight: 800;

        }

        .quick-section {

            margin-top: 35px;

        }

        .quick-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

        }

        .quick-card {

            background: white;

            border:
                1px solid
                #e5e7eb;

            border-radius: 18px;

            padding: 25px;

            text-decoration: none;

            color: #1e293b;

            display: flex;

            align-items: center;

            gap: 18px;

            box-shadow:
                0 7px 25px
                rgba(0, 0, 0, 0.06);

            transition: 0.25s ease;

        }

        .quick-card:hover {

            transform: translateY(-4px);

            box-shadow:
                0 13px 32px
                rgba(0, 0, 0, 0.10);

        }

        .quick-icon {

            width: 58px;

            height: 58px;

            flex-shrink: 0;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background: #eff6ff;

            font-size: 26px;

        }

        .quick-card h3 {

            color: #102a43;

            font-size: 18px;

            margin-bottom: 6px;

        }

        .quick-card p {

            color: #64748b;

            font-size: 13px;

            line-height: 1.5;

        }

        .places-section {

            margin-top: 35px;

        }

        .places-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            margin-bottom: 18px;

        }

        .places-header .section-heading {

            margin-bottom: 0;

        }

        .add-place-btn {

            display: inline-block;

            background: #2563eb;

            color: white;

            text-decoration: none;

            padding: 11px 18px;

            border-radius: 10px;

            font-size: 14px;

            font-weight: 700;

            transition: 0.25s;

        }

        .add-place-btn:hover {

            background: #1d4ed8;

            transform: translateY(-2px);

        }

        .success-message {

            background: #dcfce7;

            color: #166534;

            border: 1px solid #bbf7d0;

            padding: 13px 16px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 600;

        }

        .error-message {

            background: #fee2e2;

            color: #991b1b;

            border: 1px solid #fecaca;

            padding: 13px 16px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 600;

        }

        .places-table-box {

            background: white;

            border:
                1px solid
                #e5e7eb;

            border-radius: 18px;

            box-shadow:
                0 8px 28px
                rgba(0, 0, 0, 0.06);

            overflow-x: auto;

        }

        .places-table {

            width: 100%;

            border-collapse: collapse;

            min-width: 750px;

        }

        .places-table th {

            background: #f8fafc;

            color: #475569;

            font-size: 13px;

            text-align: left;

            padding: 15px;

            border-bottom: 1px solid #e2e8f0;

        }

        .places-table td {

            padding: 15px;

            border-bottom: 1px solid #eef2f7;

            color: #475569;

            font-size: 14px;

            vertical-align: middle;

        }

        .places-table tr:last-child td {

            border-bottom: none;

        }

        .places-table tr:hover td {

            background: #f8fafc;

        }

        .place-thumb {

            width: 75px;

            height: 55px;

            object-fit: cover;

            border-radius: 8px;

            display: block;

            background: #e2e8f0;

        }

        .place-name {

            color: #102a43;

            font-weight: 700;

        }

        .place-location {

            color: #64748b;

            font-size: 12px;

            margin-top: 3px;

        }

        .action-buttons {

            display: flex;

            gap: 8px;

            flex-wrap: wrap;

        }

        .edit-btn,
        .delete-btn {

            display: inline-block;

            padding: 8px 12px;

            border-radius: 8px;

            text-decoration: none;

            font-size: 12px;

            font-weight: 700;

            transition: 0.2s;

        }

        .edit-btn {

            background: #dbeafe;

            color: #1d4ed8;

        }

        .edit-btn:hover {

            background: #bfdbfe;

        }

        .delete-btn {

            background: #fee2e2;

            color: #dc2626;

        }

        .delete-btn:hover {

            background: #fecaca;

        }

        .empty-places {

            padding: 45px 20px;

            text-align: center;

        }

        .empty-places-icon {

            font-size: 45px;

            margin-bottom: 12px;

        }

        .empty-places h3 {

            color: #102a43;

            margin-bottom: 7px;

        }

        .empty-places p {

            color: #64748b;

            margin-bottom: 18px;

        }

        .info-box {

            margin-top: 30px;

            background: white;

            border:
                1px solid
                #e5e7eb;

            border-radius: 19px;

            padding: 28px;

            box-shadow:
                0 8px 28px
                rgba(0, 0, 0, 0.06);

        }

        .info-box h2 {

            color: #102a43;

            font-size: 21px;

            margin-bottom: 13px;

        }

        .info-box p {

            color: #64748b;

            line-height: 1.7;

            font-size: 14px;

            margin-bottom: 7px;

        }

        .footer {

            margin-top: 35px;

            padding: 22px 0;

            text-align: center;

            color: #64748b;

            font-size: 13px;

        }

        @media (max-width: 1150px) {

            .cards {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }

        @media (max-width: 1000px) {

            .sidebar {

                width: 230px;

            }

            .main {

                margin-left: 230px;

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

            .header-badge {

                white-space: normal;

            }

            .cards {

                grid-template-columns: 1fr;

            }

            .quick-grid {

                grid-template-columns: 1fr;

            }

            .places-header {

                flex-direction: column;

                align-items: flex-start;

            }

            .welcome-banner::after {

                right: 10px;

                font-size: 75px;

            }

        }

        @media (max-width: 450px) {

            .header-text h1 {

                font-size: 28px;

            }

            .welcome-banner {

                padding: 25px 20px;

            }

            .welcome-banner h2 {

                font-size: 21px;

            }

            .dashboard-card {

                padding: 22px;

            }

            .quick-card {

                padding: 20px;

            }

            .places-header .section-heading {

                font-size: 21px;

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

        <p>

            <?php

            echo htmlspecialchars(
                $admin_email
            );

            ?>

        </p>

    </div>

    <div class="nav-title">

        Main Menu

    </div>

    <nav class="sidebar-nav">

        <a
            href="dashboard.php"
            class="active"
        >

            <span class="nav-icon">
                🏠
            </span>

            Dashboard

        </a>

        <a href="reviews.php">

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

                Admin Dashboard

            </h1>

            <p>

                Manage your ExploreWorld
                travel website from one place.

            </p>

        </div>

        <div class="header-badge">

            🔐 Secure Admin Area

        </div>

    </div>

    <section class="welcome-banner">

        <h2>

            Welcome,

            <?php

            echo htmlspecialchars(
                $admin_name
            );

            ?>

            👋

        </h2>

        <p>

            Monitor reviews, traveller
            experiences, tourist places
            and user activity.

        </p>

    </section>

    <?php if ($success_message !== ""): ?>

        <div class="success-message">

            ✅

            <?php
            echo htmlspecialchars(
                $success_message
            );
            ?>

        </div>

    <?php endif; ?>

    <?php if ($error_message !== ""): ?>

        <div class="error-message">

            ⚠️

            <?php
            echo htmlspecialchars(
                $error_message
            );
            ?>

        </div>

    <?php endif; ?>

    <h2 class="section-heading">

        📊 Website Overview

    </h2>

    <div class="cards">

        <div class="dashboard-card">

            <div class="dashboard-icon">

                ⭐

            </div>

            <h3>

                Total Reviews

            </h3>

            <p>

                <?php

                echo $total_reviews;

                ?>

            </p>

        </div>

        <div class="dashboard-card">

            <div class="dashboard-icon">

                ✍️

            </div>

            <h3>

                Total Experiences

            </h3>

            <p>

                <?php

                echo $total_experiences;

                ?>

            </p>

        </div>

        <div class="dashboard-card">

            <div class="dashboard-icon">

                👥

            </div>

            <h3>

                Total Registrations

            </h3>

            <p>

                <?php

                echo $total_users;

                ?>

            </p>

        </div>

        <div class="dashboard-card">

            <div class="dashboard-icon">

                📍

            </div>

            <h3>

                Tourist Places

            </h3>

            <p>

                <?php

                echo $total_places;

                ?>

            </p>

        </div>

    </div>

    <section class="quick-section">

        <h2 class="section-heading">

            ⚡ Quick Access

        </h2>

        <div class="quick-grid">

            <a
                href="reviews.php"
                class="quick-card"
            >

                <div class="quick-icon">

                    ⭐

                </div>

                <div>

                    <h3>

                        Manage Reviews

                    </h3>

                    <p>

                        View traveller reviews
                        and experiences submitted
                        by users.

                    </p>

                </div>

            </a>

            <a
                href="add_place.php"
                class="quick-card"
            >

                <div class="quick-icon">

                    ➕

                </div>

                <div>

                    <h3>

                        Add Tourist Place

                    </h3>

                    <p>

                        Add a new destination
                        with information, image,
                        map and activities.

                    </p>

                </div>

            </a>

        </div>

    </section>

    <section class="places-section">

        <div class="places-header">

            <h2 class="section-heading">

                📍 Manage Tourist Places

            </h2>

            <a
                href="add_place.php"
                class="add-place-btn"
            >

                ➕ Add Tourist Place

            </a>

        </div>

        <div class="places-table-box">

            <?php if ($places_query && $places_query->num_rows > 0): ?>

                <table class="places-table">

                    <thead>

                        <tr>

                            <th>
                                Image
                            </th>

                            <th>
                                Place
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Budget
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($place = $places_query->fetch_assoc()): ?>

                            <tr>

                                <td>

                                    <?php if (!empty($place["image"])): ?>

                                        <img
                                            src="../uploads/places/<?php
                                                echo htmlspecialchars(
                                                    $place["image"]
                                                );
                                            ?>"
                                            alt="<?php
                                                echo htmlspecialchars(
                                                    $place["name"]
                                                );
                                            ?>"
                                            class="place-thumb"
                                        >

                                    <?php else: ?>

                                        <div
                                            class="place-thumb"
                                            style="
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                            "
                                        >

                                            🌍

                                        </div>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="place-name">

                                        <?php

                                        echo htmlspecialchars(
                                            $place["name"]
                                        );

                                        ?>

                                    </div>

                                    <div class="place-location">

                                        📍

                                        <?php

                                        echo htmlspecialchars(
                                            $place["location"]
                                        );

                                        ?>

                                    </div>

                                </td>

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $place["category"] ?? "-"
                                    );

                                    ?>

                                </td>

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $place["budget"] ?? "-"
                                    );

                                    ?>

                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <a
                                            href="edit_place.php?id=<?php
                                                echo (int)$place["id"];
                                            ?>"
                                            class="edit-btn"
                                        >

                                            ✏️ Edit

                                        </a>

                                        <a
                                            href="delete_place.php?id=<?php
                                                echo (int)$place["id"];
                                            ?>"
                                            class="delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this place?');"
                                        >

                                            🗑️ Delete

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty-places">

                    <div class="empty-places-icon">

                        📍

                    </div>

                    <h3>

                        No Tourist Places Added

                    </h3>

                    <p>

                        Start adding destinations
                        to your ExploreWorld website.

                    </p>

                    <a
                        href="add_place.php"
                        class="add-place-btn"
                    >

                        ➕ Add First Place

                    </a>

                </div>

            <?php endif; ?>

        </div>

    </section>

    <section class="info-box">

        <h2>

            🌍 ExploreWorld Administration

        </h2>

        <p>

            From this dashboard you can
            manage tourist places, monitor
            reviews, traveller experiences
            and registered users.

        </p>

        <p>

            When a tourist place is added,
            updated or deleted, the changes
            are reflected on the user-facing
            Places page.

        </p>

    </section>

    <footer class="footer">

        © <?php echo date("Y"); ?>

        ExploreWorld —

        Discover. Explore. Experience.

    </footer>

</main>

</body>

</html>

