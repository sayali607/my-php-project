```php
<?php

require_once "../includes/auth_check.php";
require_once "../config/db.php";

if (
    !isset($_SESSION["admin_id"]) ||
    empty($_SESSION["admin_id"]) ||
    !isset($_SESSION["admin_role"]) ||
    $_SESSION["admin_role"] !== "admin"
) {

    header("Location: login.php");
    exit;
}

$admin_name = $_SESSION["admin_name"] ?? "Admin";
$admin_email = $_SESSION["admin_email"] ?? "";

$total_places = 0;

$count_result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM tourist_places"
);

if ($count_result) {

    $count_row = $count_result->fetch_assoc();

    $total_places = (int) $count_row["total"];
}

$places = $conn->query(
    "SELECT
        id,
        name,
        location,
        category,
        image,
        created_at
     FROM tourist_places
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard - ExploreWorld</title>

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

            min-height: 100vh;

        }

        .navbar {

            background: #102a43;

            padding: 18px 7%;

            display: flex;

            justify-content: space-between;

            align-items: center;

            position: sticky;

            top: 0;

            z-index: 1000;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.12);

        }

        .logo {

            color: white;

            text-decoration: none;

            font-size: 27px;

            font-weight: 700;

        }

        .logo span {

            color: #60a5fa;

        }

        .nav-links {

            display: flex;

            align-items: center;

            gap: 22px;

        }

        .nav-links a {

            color: white;

            text-decoration: none;

            font-weight: 600;

            transition: 0.2s;

        }

        .nav-links a:hover {

            color: #93c5fd;

        }

        .logout-link {

            background: #dc3545;

            padding: 9px 16px;

            border-radius: 9px;

        }

        .logout-link:hover {

            background: #b91c1c;

            color: white !important;

        }

        .container {

            width: 88%;

            max-width: 1200px;

            margin: auto;

            padding: 55px 0 70px;

        }

        .dashboard-header {

            margin-bottom: 35px;

        }

        .dashboard-header h1 {

            font-size: 38px;

            color: #102a43;

            margin-bottom: 8px;

        }

        .dashboard-header p {

            color: #64748b;

            font-size: 16px;

        }

        .welcome-card {

            background: white;

            border-radius: 20px;

            padding: 35px;

            margin-bottom: 35px;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.08);

            border-left: 5px solid #2563eb;

        }

        .welcome-card h2 {

            color: #102a43;

            font-size: 26px;

            margin-bottom: 10px;

        }

        .welcome-card > p {

            color: #64748b;

            margin-bottom: 20px;

        }

        .user-info {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

        }

        .info-box {

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            padding: 18px;

            border-radius: 12px;

        }

        .info-box strong {

            display: block;

            color: #102a43;

            margin-bottom: 5px;

        }

        .info-box span {

            color: #64748b;

        }

        .section-title {

            font-size: 28px;

            color: #102a43;

            margin-bottom: 20px;

        }

        .action-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 25px;

        }

        .action-card {

            background: white;

            padding: 30px;

            border-radius: 18px;

            text-decoration: none;

            color: #1f2937;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.07);

            transition: 0.3s;

        }

        .action-card:hover {

            transform: translateY(-6px);

            box-shadow:
                0 14px 32px
                rgba(0, 0, 0, 0.12);

        }

        .action-icon {

            width: 58px;

            height: 58px;

            border-radius: 50%;

            background: #eff6ff;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 27px;

            margin-bottom: 18px;

        }

        .action-card h3 {

            color: #102a43;

            font-size: 20px;

            margin-bottom: 8px;

        }

        .action-card p {

            color: #64748b;

            font-size: 14px;

            line-height: 1.6;

        }

        .places-box {

            margin-top: 35px;

            background: white;

            padding: 30px;

            border-radius: 20px;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.07);

        }

        .places-box h2 {

            color: #102a43;

            margin-bottom: 20px;

        }

        .place-row {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding: 18px 0;

            border-bottom: 1px solid #e5e7eb;

        }

        .place-row:last-child {

            border-bottom: none;

        }

        .place-info h3 {

            color: #102a43;

            margin-bottom: 4px;

        }

        .place-info p {

            color: #64748b;

            font-size: 14px;

        }

        .place-actions {

            display: flex;

            gap: 8px;

            flex-shrink: 0;

        }

        .edit-button,
        .delete-button {

            display: inline-block;

            padding: 9px 14px;

            border-radius: 8px;

            text-decoration: none;

            color: white;

            font-size: 13px;

            font-weight: bold;

        }

        .edit-button {

            background: #2563eb;

        }

        .edit-button:hover {

            background: #1d4ed8;

        }

        .delete-button {

            background: #dc3545;

        }

        .delete-button:hover {

            background: #b91c1c;

        }

        .feature-box {

            margin-top: 35px;

            background:
                linear-gradient(
                    135deg,
                    #102a43,
                    #1e3a5f
                );

            color: white;

            padding: 35px;

            border-radius: 20px;

            box-shadow:
                0 10px 30px
                rgba(16, 42, 67, 0.20);

        }

        .feature-box h2 {

            font-size: 25px;

            margin-bottom: 10px;

        }

        .feature-box p {

            color: #dbeafe;

            margin-bottom: 22px;

        }

        .explore-button {

            display: inline-block;

            background: #2563eb;

            color: white;

            padding: 13px 22px;

            border-radius: 10px;

            text-decoration: none;

            font-weight: bold;

            transition: 0.2s;

        }

        .explore-button:hover {

            background: #1d4ed8;

            transform: translateY(-2px);

        }

        .footer {

            text-align: center;

            padding: 25px;

            color: #64748b;

            font-size: 14px;

        }

        @media (max-width: 850px) {

            .action-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }

            .user-info {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }

        @media (max-width: 650px) {

            .navbar {

                flex-direction: column;

                gap: 15px;

            }

            .nav-links {

                flex-wrap: wrap;

                justify-content: center;

            }

            .container {

                width: 92%;

                padding-top: 35px;

            }

            .dashboard-header h1 {

                font-size: 30px;

            }

            .user-info {

                grid-template-columns: 1fr;

            }

            .action-grid {

                grid-template-columns: 1fr;

            }

            .welcome-card,
            .feature-box,
            .places-box {

                padding: 25px;

            }

            .place-row {

                flex-direction: column;

                align-items: flex-start;

            }

            .place-actions {

                width: 100%;

            }

            .edit-button,
            .delete-button {

                flex: 1;

                text-align: center;

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
            Home
        </a>

        <a href="../places/index.php">
            Places
        </a>

        <a
            href="logout.php"
            class="logout-link"
        >
            Logout
        </a>

    </div>

</nav>

<main class="container">

    <div class="dashboard-header">

        <h1>
            Admin Dashboard
        </h1>

        <p>
            Manage ExploreWorld tourist destinations
            and keep the platform information updated.
        </p>

    </div>

    <div class="welcome-card">

        <h2>

            Welcome,
            <?php
            echo htmlspecialchars($admin_name);
            ?>
            👋

        </h2>

        <p>

            You are logged in as an ExploreWorld administrator.

        </p>

        <div class="user-info">

            <div class="info-box">

                <strong>
                    👤 Admin Name
                </strong>

                <span>

                    <?php
                    echo htmlspecialchars($admin_name);
                    ?>

                </span>

            </div>

            <div class="info-box">

                <strong>
                    📧 Email Address
                </strong>

                <span>

                    <?php
                    echo htmlspecialchars($admin_email);
                    ?>

                </span>

            </div>

            <div class="info-box">

                <strong>
                    🌍 Total Places
                </strong>

                <span>

                    <?php
                    echo $total_places;
                    ?>

                    Tourist Places

                </span>

            </div>

        </div>

    </div>

    <h2 class="section-title">
        Place Management
    </h2>

    <div class="action-grid">

        <a
            href="add_place.php"
            class="action-card"
        >

            <div class="action-icon">
                ➕
            </div>

            <h3>
                Add Tourist Place
            </h3>

            <p>
                Add a new destination with its
                information, image, map and activities.
            </p>

        </a>

        <a
            href="../places/index.php"
            class="action-card"
        >

            <div class="action-icon">
                🌍
            </div>

            <h3>
                View Places
            </h3>

            <p>
                Open the user side and check how
                tourist destinations are displayed.
            </p>

        </a>

        <a
            href="#places"
            class="action-card"
        >

            <div class="action-icon">
                ✏️
            </div>

            <h3>
                Manage Places
            </h3>

            <p>
                Update existing tourist places or
                remove destinations from the system.
            </p>

        </a>

    </div>

    <div
        class="places-box"
        id="places"
    >

        <h2>
            🌍 Existing Tourist Places
        </h2>

        <?php if ($places && $places->num_rows > 0): ?>

            <?php while ($place = $places->fetch_assoc()): ?>

                <div class="place-row">

                    <div class="place-info">

                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $place["name"]
                            );
                            ?>

                        </h3>

                        <p>

                            📍

                            <?php
                            echo htmlspecialchars(
                                $place["location"]
                            );
                            ?>

                            <?php if (!empty($place["category"])): ?>

                                &nbsp; • &nbsp;

                                🏷️

                                <?php
                                echo htmlspecialchars(
                                    $place["category"]
                                );
                                ?>

                            <?php endif; ?>

                        </p>

                    </div>

                    <div class="place-actions">

                        <a
                            href="edit_place.php?id=<?php
                                echo (int) $place["id"];
                            ?>"
                            class="edit-button"
                        >

                            ✏️ Edit

                        </a>

                        <a
                            href="delete_place.php?id=<?php
                                echo (int) $place["id"];
                            ?>"
                            class="delete-button"
                            onclick="return confirm(
                                'Are you sure you want to delete this tourist place?'
                            );"
                        >

                            🗑️ Delete

                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p style="
                color:#64748b;
                text-align:center;
                padding:20px;
            ">

                No tourist places have been added yet.

            </p>

        <?php endif; ?>

    </div>

    <div class="feature-box">

        <h2>
            ✈️ Keep ExploreWorld Updated
        </h2>

        <p>
            Add new destinations, update existing
            information and manage tourist places
            from the admin panel.
        </p>

        <a
            href="add_place.php"
            class="explore-button"
        >

            Add New Tourist Place →

        </a>

    </div>

</main>

<footer class="footer">

    © <?php echo date("Y"); ?>
    ExploreWorld.
    Admin Panel.

</footer>

</body>

</html>
```
