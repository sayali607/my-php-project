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



$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $name = trim($_POST["name"] ?? "");

    $location = trim($_POST["location"] ?? "");

    $category = trim($_POST["category"] ?? "");

    $budget = trim($_POST["budget"] ?? "");

    $description = trim($_POST["description"] ?? "");

    $best_time = trim($_POST["best_time"] ?? "");

    $activities = trim($_POST["activities"] ?? "");

    $map_link = trim($_POST["map_link"] ?? "");

    $image = trim($_POST["image"] ?? "");



    if (
        $name === "" ||
        $location === "" ||
        $category === "" ||
        $description === ""
    ) {

        $error = "Please fill all required fields.";

    } else {


        $stmt = $conn->prepare(
            "INSERT INTO tourist_places
            (
                name,
                location,
                category,
                budget,
                description,
                best_time,
                activities,
                image,
                map_link
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );


        if (!$stmt) {

            $error = "Unable to prepare place information.";

        } else {


            $stmt->bind_param(
                "sssssssss",
                $name,
                $location,
                $category,
                $budget,
                $description,
                $best_time,
                $activities,
                $image,
                $map_link
            );


            if ($stmt->execute()) {

                $stmt->close();

                header(
                    "Location: dashboard.php?place=added"
                );

                exit;

            } else {

                $error =
                    "Unable to add tourist place. Please try again.";

                $stmt->close();
            }
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

    <title>Add Tourist Place - ExploreWorld</title>


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

        }


        .nav-links a:hover {

            color: #93c5fd;

        }


        .back-link {

            background: #2563eb;

            padding: 9px 16px;

            border-radius: 9px;

        }


        .back-link:hover {

            background: #1d4ed8;

            color: white !important;

        }


        .container {

            width: 88%;

            max-width: 850px;

            margin: auto;

            padding: 55px 0 70px;

        }


        .page-header {

            margin-bottom: 30px;

        }


        .page-header h1 {

            color: #102a43;

            font-size: 36px;

            margin-bottom: 8px;

        }


        .page-header p {

            color: #64748b;

        }


       
        .form-card {

            background: white;

            padding: 35px;

            border-radius: 20px;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.08);

            border: 1px solid #e5e7eb;

        }


        .error-message {

            background: #fee2e2;

            color: #b91c1c;

            padding: 12px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;

        }



        .form-group {

            margin-bottom: 20px;

        }


        .form-row {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 18px;

        }


        label {

            display: block;

            color: #102a43;

            font-weight: 700;

            margin-bottom: 7px;

            font-size: 14px;

        }


        input,
        textarea,
        select {

            width: 100%;

            padding: 13px 14px;

            border: 1px solid #dbe2e8;

            border-radius: 9px;

            outline: none;

            font-family: inherit;

            font-size: 14px;

            color: #1f2937;

            background: white;

        }


        textarea {

            min-height: 120px;

            resize: vertical;

        }


        input:focus,
        textarea:focus,
        select:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);

        }


        .required {

            color: #dc3545;

        }


        .help-text {

            color: #94a3b8;

            font-size: 12px;

            margin-top: 5px;

        }


        .form-actions {

            display: flex;

            gap: 12px;

            margin-top: 25px;

        }


        .submit-button {

            border: none;

            background: #2563eb;

            color: white;

            padding: 13px 22px;

            border-radius: 10px;

            font-weight: bold;

            cursor: pointer;

            font-size: 14px;

        }


        .submit-button:hover {

            background: #1d4ed8;

        }


        .cancel-button {

            background: #e5e7eb;

            color: #334155;

            padding: 13px 22px;

            border-radius: 10px;

            text-decoration: none;

            font-weight: bold;

            font-size: 14px;

        }


        .cancel-button:hover {

            background: #d1d5db;

        }



        .footer {

            text-align: center;

            padding: 25px;

            color: #64748b;

            font-size: 14px;

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


            .form-card {

                padding: 25px;

            }


            .form-row {

                grid-template-columns: 1fr;

            }


            .form-actions {

                flex-direction: column;

            }


            .submit-button,
            .cancel-button {

                width: 100%;

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

        <a href="dashboard.php">
            Dashboard
        </a>


        <a
            href="logout.php"
            class="back-link"
        >
            Logout
        </a>

    </div>


</nav>




<main class="container">


    <div class="page-header">

        <h1>
            Add Tourist Place
        </h1>

        <p>
            Add a new destination to ExploreWorld.
        </p>

    </div>



    <div class="form-card">


        <?php if ($error !== ""): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>



        <form
            method="POST"
            action=""
        >


            <!-- NAME -->

            <div class="form-group">

                <label>

                    Place Name
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Example: Goa"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["name"] ?? ""
                        );
                    ?>"
                    required
                >

            </div>



            <!-- LOCATION + CATEGORY -->

            <div class="form-row">


                <div class="form-group">

                    <label>

                        Location
                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="location"
                        placeholder="Example: Goa, India"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["location"] ?? ""
                            );
                        ?>"
                        required
                    >

                </div>



                <div class="form-group">

                    <label>

                        Category
                        <span class="required">*</span>

                    </label>

                    <input
                        type="text"
                        name="category"
                        placeholder="Example: Beach"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["category"] ?? ""
                            );
                        ?>"
                        required
                    >

                </div>


            </div>



            <!-- BUDGET + BEST TIME -->

            <div class="form-row">


                <div class="form-group">

                    <label>
                        Budget
                    </label>

                    <input
                        type="text"
                        name="budget"
                        placeholder="Example: ₹5,000 - ₹10,000"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["budget"] ?? ""
                            );
                        ?>"
                    >

                </div>



                <div class="form-group">

                    <label>
                        Best Time
                    </label>

                    <input
                        type="text"
                        name="best_time"
                        placeholder="Example: October - March"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["best_time"] ?? ""
                            );
                        ?>"
                    >

                </div>


            </div>



            <!-- DESCRIPTION -->

            <div class="form-group">

                <label>

                    Description
                    <span class="required">*</span>

                </label>

                <textarea
                    name="description"
                    placeholder="Write information about this tourist place..."
                    required
                ><?php
                    echo htmlspecialchars(
                        $_POST["description"] ?? ""
                    );
                ?></textarea>

            </div>



            <!-- ACTIVITIES -->

            <div class="form-group">

                <label>
                    Activities
                </label>

                <textarea
                    name="activities"
                    placeholder="Example: Beach activities, sightseeing, trekking..."
                ><?php
                    echo htmlspecialchars(
                        $_POST["activities"] ?? ""
                    );
                ?></textarea>

            </div>



            <!-- IMAGE -->

            <div class="form-group">

                <label>
                    Image File Name
                </label>

                <input
                    type="text"
                    name="image"
                    placeholder="Example: goa.jpg"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["image"] ?? ""
                        );
                    ?>"
                >

                <div class="help-text">

                    Keep the image inside your
                    <strong>places</strong> folder.

                </div>

            </div>



            <!-- MAP -->

            <div class="form-group">

                <label>
                    Google Maps Link
                </label>

                <input
                    type="text"
                    name="map_link"
                    placeholder="Paste Google Maps URL"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["map_link"] ?? ""
                        );
                    ?>"
                >

            </div>



            <!-- BUTTONS -->

            <div class="form-actions">


                <button
                    type="submit"
                    class="submit-button"
                >

                    ➕ Add Tourist Place

                </button>


                <a
                    href="dashboard.php"
                    class="cancel-button"
                >

                    Cancel

                </a>


            </div>


        </form>


    </div>


</main>




<footer class="footer">

    © <?php echo date("Y"); ?>
    ExploreWorld.
    Admin Panel.

</footer>


</body>

</html>