```php
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../includes/user_auth_check.php";

require_once "../config/db.php";

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if ($search !== "") {

    $sql = "
        SELECT *
        FROM tourist_places
        WHERE
            name LIKE ?
            OR location LIKE ?
            OR category LIKE ?
        ORDER BY id DESC
    ";

    $stmt = $conn->prepare($sql);

    $search_value = "%" . $search . "%";

    $stmt->bind_param(
        "sss",
        $search_value,
        $search_value,
        $search_value
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "
        SELECT *
        FROM tourist_places
        ORDER BY id DESC
    ";

    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Explore Places - ExploreWorld</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f7fa;
    color: #333;
}

.navbar {
    width: 100%;
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 6%;
    box-shadow: 0 3px 12px rgba(0,0,0,0.15);
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.logo-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    object-fit: cover;
    display: block;
    border: 3px solid #20c997;
    background: #ffffff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.25);
    flex-shrink: 0;
}

.logo-text {
    font-size: 24px;
    font-weight: bold;
    color: white;
}

.logo-text span {
    color: #20c997;
}

.nav-links {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 25px;
}

.nav-links li a {
    color: white;
    text-decoration: none;
    font-size: 15px;
    transition: 0.3s;
}

.nav-links li a:hover {
    color: #20c997;
}

.page-header {
    background: linear-gradient(
        135deg,
        #0f766e,
        #20c997
    );

    color: white;
    text-align: center;

    padding: 60px 20px;
}

.page-header h1 {
    font-size: 42px;
    margin-bottom: 12px;
}

.page-header p {
    font-size: 17px;
    opacity: 0.95;
}

.search-section {
    width: 90%;
    max-width: 1000px;

    margin: -30px auto 40px;

    background: white;

    padding: 20px;

    border-radius: 12px;

    box-shadow: 0 5px 20px rgba(0,0,0,0.12);
}

.search-form {
    display: flex;
    gap: 10px;
}

.search-form input {
    flex: 1;

    padding: 14px 16px;

    border: 1px solid #ddd;

    border-radius: 8px;

    font-size: 16px;

    outline: none;
}

.search-form input:focus {
    border-color: #20c997;
}

.search-form button {
    padding: 14px 25px;

    border: none;

    border-radius: 8px;

    background: #20c997;

    color: white;

    font-size: 16px;

    cursor: pointer;

    transition: 0.3s;
}

.search-form button:hover {
    background: #159d78;
}

.container {
    width: 90%;
    max-width: 1200px;

    margin: auto;

    padding-bottom: 60px;
}

.section-title {
    text-align: center;

    margin-bottom: 30px;
}

.section-title h2 {
    font-size: 32px;

    color: #111827;

    margin-bottom: 8px;
}

.section-title p {
    color: #777;
}

.places-grid {
    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(280px, 1fr));

    gap: 28px;
}

.place-card {
    background: white;

    border-radius: 15px;

    overflow: hidden;

    box-shadow:
        0 5px 18px rgba(0,0,0,0.10);

    transition: 0.3s;

    display: flex;

    flex-direction: column;
}

.place-card:hover {
    transform: translateY(-7px);

    box-shadow:
        0 10px 28px rgba(0,0,0,0.15);
}

.place-image {
    width: 100%;

    height: 220px;

    object-fit: cover;

    display: block;
}

.place-content {
    padding: 22px;

    display: flex;

    flex-direction: column;

    flex: 1;
}

.place-content h3 {
    font-size: 25px;

    color: #111827;

    margin-bottom: 10px;
}

.place-location {
    color: #20a67a;

    font-weight: bold;

    margin-bottom: 12px;
}

.place-category {
    display: inline-block;

    background: #e7f8f2;

    color: #16815f;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 13px;

    margin-bottom: 12px;

    width: fit-content;
}

.place-description {
    color: #666;

    line-height: 1.6;

    margin-bottom: 20px;
}

.details-btn {
    display: inline-block;

    text-align: center;

    margin-top: auto;

    padding: 12px 18px;

    background: #20c997;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: bold;

    transition: 0.3s;
}

.details-btn:hover {
    background: #159d78;
}

.no-places {
    background: white;

    padding: 50px;

    text-align: center;

    border-radius: 12px;

    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.no-places h3 {
    margin-bottom: 10px;

    color: #111827;
}

.no-places p {
    color: #777;
}

footer {
    background: #111827;

    color: white;

    text-align: center;

    padding: 25px 20px;

    margin-top: 30px;
}

footer p {
    margin: 5px 0;

    color: #ddd;
}

@media (max-width: 768px) {

    .navbar {
        flex-direction: column;

        gap: 15px;
    }

    .nav-links {
        flex-wrap: wrap;

        justify-content: center;

        gap: 15px;
    }

    .page-header h1 {
        font-size: 32px;
    }

    .search-form {
        flex-direction: column;
    }

    .search-form button {
        width: 100%;
    }

}

</style>

</head>

<body>

<header class="navbar">

    <a href="../index.php" class="logo">

        <img
            src="../assets/images/travel-icon.png"
            alt="ExploreWorld"
            class="logo-icon"
        >

        <div class="logo-text">
            Explore<span>World</span>
        </div>

    </a>

    <ul class="nav-links">

        <li>
            <a href="../index.php">
                Home
            </a>
        </li>

        <li>
            <a href="index.php">
                Places
            </a>
        </li>

        <li>
            <a href="../user/dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="../auth/logout.php">
                Logout
            </a>
        </li>

    </ul>

</header>

<section class="page-header">

    <h1>Explore Places 🌍</h1>

    <p>
        Discover beautiful destinations and plan your next adventure.
    </p>

</section>

<section class="search-section">

    <form
        method="GET"
        action="index.php"
        class="search-form"
    >

        <input
            type="text"
            name="search"
            placeholder="Search places, location or category..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            🔎 Search
        </button>

    </form>

</section>

<main class="container">

    <div class="section-title">

        <h2>
            Tourist Destinations
        </h2>

        <p>
            Find your favourite destination and explore more details.
        </p>

    </div>

    <?php if ($result && $result->num_rows > 0): ?>

        <div class="places-grid">

            <?php while ($place = $result->fetch_assoc()): ?>

                <?php

                $image_name = basename(
                    trim((string)($place["image"] ?? ""))
                );

                if ($image_name !== "") {

                    $places_file =
                        __DIR__
                        . DIRECTORY_SEPARATOR
                        . $image_name;

                    $upload_file =
                        dirname(__DIR__)
                        . DIRECTORY_SEPARATOR
                        . "uploads"
                        . DIRECTORY_SEPARATOR
                        . "places"
                        . DIRECTORY_SEPARATOR
                        . $image_name;

                    if (is_file($places_file)) {

                        $image_url = $image_name;

                    } elseif (is_file($upload_file)) {

                        $image_url =
                            "../uploads/places/"
                            . rawurlencode($image_name);

                    } else {

                        $image_url =
                            "../assets/images/hero.jpg";
                    }

                } else {

                    $image_url =
                        "../assets/images/hero.jpg";
                }

                ?>

                <div class="place-card">

                    <img
                        src="<?php echo htmlspecialchars($image_url); ?>"
                        alt="<?php echo htmlspecialchars($place["name"]); ?>"
                        class="place-image"
                    >

                    <div class="place-content">

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $place["name"]
                            );
                            ?>
                        </h3>

                        <div class="place-location">

                            📍
                            <?php
                            echo htmlspecialchars(
                                $place["location"]
                            );
                            ?>

                        </div>

                        <?php if (!empty($place["category"])): ?>

                            <div class="place-category">

                                <?php
                                echo htmlspecialchars(
                                    $place["category"]
                                );
                                ?>

                            </div>

                        <?php endif; ?>

                        <p class="place-description">

                            <?php

                            $description =
                                $place["description"]
                                ?? "Explore this beautiful tourist destination.";

                            echo htmlspecialchars(
                                mb_strimwidth(
                                    $description,
                                    0,
                                    140,
                                    "..."
                                )
                            );

                            ?>

                        </p>

                        <a
                            href="details.php?id=<?php echo (int)$place["id"]; ?>"
                            class="details-btn"
                        >
                            Explore Details →
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="no-places">

            <h3>
                No Places Found 😔
            </h3>

            <p>
                Try searching with another place name,
                location or category.
            </p>

        </div>

    <?php endif; ?>

</main>

<footer>

    <p>
        © <?php echo date("Y"); ?>
        ExploreWorld - Tourist Recommendation System
    </p>

    <p>
        Discover • Explore • Experience
    </p>

</footer>

</body>

</html>
```
