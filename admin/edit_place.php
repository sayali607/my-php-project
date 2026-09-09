<?php

session_start();

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


$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
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
        map_link
     FROM tourist_places
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit;
}

$place = $result->fetch_assoc();


$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $budget = trim($_POST["budget"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $best_time = trim($_POST["best_time"] ?? "");
    $activities = trim($_POST["activities"] ?? "");
    $map_link = trim($_POST["map_link"] ?? "");


    if (
        $name === "" ||
        $location === "" ||
        $category === "" ||
        $description === ""
    ) {

        $error = "Please fill all required fields.";

    } else {

        $update = $conn->prepare(
            "UPDATE tourist_places
             SET
                name = ?,
                location = ?,
                category = ?,
                budget = ?,
                description = ?,
                best_time = ?,
                activities = ?,
                map_link = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "ssssssssi",
            $name,
            $location,
            $category,
            $budget,
            $description,
            $best_time,
            $activities,
            $map_link,
            $id
        );


        if ($update->execute()) {

            $message = "Place updated successfully.";

            // Refresh data
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
                    map_link
                 FROM tourist_places
                 WHERE id = ?"
            );

            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            $place = $result->fetch_assoc();

        } else {

            $error = "Unable to update place.";

        }

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

    <title>Edit Place - ExploreWorld</title>


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

        }


                .navbar {

            background: #102a43;

            padding: 18px 7%;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .logo {

            color: white;

            text-decoration: none;

            font-size: 27px;

            font-weight: bold;

        }


        .logo span {

            color: #20c997;

        }


        .nav-links {

            display: flex;

            gap: 15px;

        }


        .nav-links a {

            color: white;

            text-decoration: none;

            padding: 9px 15px;

            border-radius: 8px;

            font-weight: bold;

        }


        .nav-links a:hover {

            background: rgba(255,255,255,0.1);

            color: #20c997;

        }


       
        .container {

            width: 90%;

            max-width: 900px;

            margin: auto;

            padding: 50px 0;

        }


        .page-title {

            text-align: center;

            margin-bottom: 30px;

        }


        .page-title h1 {

            color: #102a43;

            font-size: 35px;

            margin-bottom: 8px;

        }


        .page-title p {

            color: #64748b;

        }


               .form-card {

            background: white;

            padding: 35px;

            border-radius: 18px;

            box-shadow:
                0 8px 25px
                rgba(0,0,0,0.08);

        }


        .form-group {

            margin-bottom: 20px;

        }


        .form-group label {

            display: block;

            color: #102a43;

            font-weight: bold;

            margin-bottom: 8px;

        }


        .form-group input,
        .form-group textarea {

            width: 100%;

            padding: 13px 14px;

            border: 1px solid #dbe2e8;

            border-radius: 9px;

            font-size: 15px;

            outline: none;

            font-family: Arial, Helvetica, sans-serif;

        }


        .form-group textarea {

            min-height: 120px;

            resize: vertical;

        }


        .form-group input:focus,
        .form-group textarea:focus {

            border-color: #20c997;

            box-shadow:
                0 0 0 3px
                rgba(32,201,151,0.12);

        }


      
        .current-image {

            margin-bottom: 20px;

        }


        .current-image img {

            width: 220px;

            height: 140px;

            object-fit: cover;

            border-radius: 10px;

            border: 1px solid #ddd;

        }


             .success {

            background: #e8fff5;

            color: #16835f;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;

        }


        .error {

            background: #ffe8e8;

            color: #c0392b;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: center;

        }


              .buttons {

            display: flex;

            gap: 12px;

            margin-top: 25px;

        }


        .btn {

            border: none;

            padding: 13px 22px;

            border-radius: 9px;

            text-decoration: none;

            font-weight: bold;

            cursor: pointer;

            font-size: 15px;

        }


        .update-btn {

            background: #20c997;

            color: white;

        }


        .update-btn:hover {

            background: #159f7b;

        }


        .cancel-btn {

            background: #64748b;

            color: white;

        }


        .cancel-btn:hover {

            background: #475569;

        }


       
        @media (max-width: 600px) {

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

                padding: 35px 0;

            }


            .form-card {

                padding: 25px 20px;

            }


            .page-title h1 {

                font-size: 29px;

            }


            .buttons {

                flex-direction: column;

            }


            .btn {

                text-align: center;

            }

        }

    </style>

</head>


<body>


<nav class="navbar">

    <a
        href="dashboard.php"
        class="logo"
    >
        Explore<span>World</span>
    </a>


    <div class="nav-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>



<main class="container">


    <div class="page-title">

        <h1>
            Edit Tourist Place
        </h1>

        <p>
            Update destination information
        </p>

    </div>



    <div class="form-card">


        <?php if ($message !== ""): ?>

            <div class="success">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <?php if ($error !== ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>



        <!-- CURRENT IMAGE -->

        <?php if (!empty($place["image"])): ?>

            <div class="current-image">

                <label>
                    Current Image
                </label>

                <br><br>

                <img
                    src="../uploads/places/<?php
                        echo htmlspecialchars($place["image"]);
                    ?>"
                    alt="Place Image"
                >

            </div>

        <?php endif; ?>



        <!-- FORM -->

        <form method="POST">


            <div class="form-group">

                <label>
                    Place Name *
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php
                        echo htmlspecialchars($place["name"]);
                    ?>"
                    required
                >

            </div>



            <div class="form-group">

                <label>
                    Location *
                </label>

                <input
                    type="text"
                    name="location"
                    value="<?php
                        echo htmlspecialchars($place["location"]);
                    ?>"
                    required
                >

            </div>



            <div class="form-group">

                <label>
                    Category *
                </label>

                <input
                    type="text"
                    name="category"
                    value="<?php
                        echo htmlspecialchars($place["category"]);
                    ?>"
                    required
                >

            </div>



            <div class="form-group">

                <label>
                    Budget
                </label>

                <input
                    type="text"
                    name="budget"
                    value="<?php
                        echo htmlspecialchars($place["budget"]);
                    ?>"
                >

            </div>



            <div class="form-group">

                <label>
                    Description *
                </label>

                <textarea
                    name="description"
                    required
                ><?php
                    echo htmlspecialchars($place["description"]);
                ?></textarea>

            </div>



            <div class="form-group">

                <label>
                    Best Time
                </label>

                <input
                    type="text"
                    name="best_time"
                    value="<?php
                        echo htmlspecialchars($place["best_time"]);
                    ?>"
                >

            </div>



            <div class="form-group">

                <label>
                    Activities
                </label>

                <textarea
                    name="activities"
                ><?php
                    echo htmlspecialchars($place["activities"]);
                ?></textarea>

            </div>



            <div class="form-group">

                <label>
                    Google Map Link
                </label>

                <input
                    type="url"
                    name="map_link"
                    value="<?php
                        echo htmlspecialchars($place["map_link"]);
                    ?>"
                >

            </div>



            <div class="buttons">

                <button
                    type="submit"
                    class="btn update-btn"
                >
                    Update Place
                </button>


                <a
                    href="dashboard.php"
                    class="btn cancel-btn"
                >
                    Cancel
                </a>

            </div>


        </form>


    </div>


</main>


</body>

</html>