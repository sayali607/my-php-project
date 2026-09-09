<?php

session_start();

require_once "../config/db.php";

$message = "";

if (
    isset($_SESSION["admin_id"]) &&
    isset($_SESSION["admin_role"]) &&
    $_SESSION["admin_role"] === "admin"
) {

    header("Location: dashboard.php");
    exit;

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $message =
            "Please enter your email and password.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message =
            "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare(
            "SELECT
                id,
                name,
                email,
                password
             FROM admins
             WHERE email = ?
             LIMIT 1"
        );

        if ($stmt === false) {

            $message =
                "Unable to connect to the admin database.";

        } else {

            $stmt->bind_param(
                "s",
                $email
            );

            $stmt->execute();

            $result = $stmt->get_result();

            if (
                $result &&
                $result->num_rows === 1
            ) {

                $admin =
                    $result->fetch_assoc();

                if (
                    password_verify(
                        $password,
                        $admin["password"]
                    )
                ) {

                    session_regenerate_id(true);

                    $_SESSION["admin_id"] =
                        (int)$admin["id"];

                    $_SESSION["admin_name"] =
                        $admin["name"];

                    $_SESSION["admin_email"] =
                        $admin["email"];

                    $_SESSION["admin_role"] =
                        "admin";

                    header(
                        "Location: dashboard.php"
                    );

                    exit;

                } else {

                    $message =
                        "Incorrect admin password.";

                }

            } else {

                $message =
                    "Admin account not found.";

            }

            $stmt->close();

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

    <title>
        Admin Login - ExploreWorld
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

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 30px 18px;

            background:
                linear-gradient(
                    135deg,
                    #eef6ff,
                    #f8fafc
                );

            color: #1e293b;

        }

        .page-wrapper {

            width: 100%;

            max-width: 470px;

        }

               .logo-section {

            text-align: center;

            margin-bottom: 22px;

        }

        .logo {

            display: inline-block;

            text-decoration: none;

            color: #102a43;

            font-size: 34px;

            font-weight: 800;

            letter-spacing: 0.2px;

        }

        .logo span {

            color: #2563eb;

        }

        .logo-tagline {

            color: #64748b;

            font-size: 12px;

            margin-top: 6px;

            letter-spacing: 0.8px;

            font-weight: 600;

        }

        .login-card {

            background: white;

            border:
                1px solid
                #e2e8f0;

            border-radius: 24px;

            padding: 38px;

            box-shadow:
                0 18px 45px
                rgba(15, 42, 67, 0.10);

        }

              .badge-wrapper {

            margin-bottom: 18px;

        }

        .badge {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 8px 13px;

            background: #eff6ff;

            color: #2563eb;

            border:
                1px solid
                #dbeafe;

            border-radius: 50px;

            font-size: 11px;

            font-weight: 800;

            letter-spacing: 0.4px;

        }

              .login-card h1 {

            color: #102a43;

            font-size: 31px;

            margin-bottom: 9px;

        }

        .subtitle {

            color: #64748b;

            line-height: 1.6;

            font-size: 14px;

            margin-bottom: 27px;

        }

        .error {

            background: #fef2f2;

            border:
                1px solid
                #fecaca;

            color: #b91c1c;

            padding: 13px 14px;

            border-radius: 11px;

            margin-bottom: 20px;

            font-size: 13px;

            line-height: 1.5;

        }

        .form-group {

            margin-bottom: 20px;

        }

        label {

            display: block;

            color: #334155;

            font-size: 14px;

            font-weight: 700;

            margin-bottom: 8px;

        }

        input {

            width: 100%;

            padding: 14px 15px;

            border:
                1px solid
                #cbd5e1;

            border-radius: 11px;

            background: #ffffff;

            color: #1e293b;

            font-size: 15px;

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }

        input::placeholder {

            color: #94a3b8;

        }

        input:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 4px
                rgba(37, 99, 235, 0.10);

        }

        .login-button {

            width: 100%;

            border: none;

            padding: 15px;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #102a43,
                    #2563eb
                );

            color: white;

            font-size: 15px;

            font-weight: 800;

            cursor: pointer;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;

            box-shadow:
                0 8px 18px
                rgba(37, 99, 235, 0.18);

        }

        .login-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(37, 99, 235, 0.25);

        }

        .login-button:active {

            transform: translateY(0);

        }

              .back {

            text-align: center;

            margin-top: 23px;

            padding-top: 20px;

            border-top:
                1px solid
                #e2e8f0;

        }

        .back a {

            color: #2563eb;

            text-decoration: none;

            font-size: 14px;

            font-weight: 700;

            transition: 0.2s;

        }

        .back a:hover {

            color: #1d4ed8;

            text-decoration: underline;

        }

            .security-note {

            text-align: center;

            margin-top: 17px;

            color: #64748b;

            font-size: 11px;

            line-height: 1.5;

        }

               @media (max-width: 520px) {

            body {

                padding: 22px 14px;

            }

            .logo {

                font-size: 29px;

            }

            .login-card {

                padding: 28px 22px;

                border-radius: 20px;

            }

            .login-card h1 {

                font-size: 27px;

            }

        }

    </style>

</head>

<body>

<div class="page-wrapper">

    <div class="logo-section">

        <a
            href="../index.php"
            class="logo"
        >

            Explore<span>World</span>

        </a>

        <div class="logo-tagline">

            DISCOVER • EXPLORE • EXPERIENCE

        </div>

    </div>

    <div class="login-card">

        <div class="badge-wrapper">

            <div class="badge">

                🔐

                AUTHORIZED ADMIN AREA

            </div>

        </div>

        <h1>

            Admin Login

        </h1>

        <p class="subtitle">

            Sign in to manage your
            ExploreWorld website,
            places and traveller reviews.

        </p>

        <?php if ($message !== ""): ?>

            <div class="error">

                <?php

                echo htmlspecialchars(
                    $message
                );

                ?>

            </div>

        <?php endif; ?>

        <form
            method="POST"
            action=""
        >

            <div class="form-group">

                <label for="email">

                    Admin Email

                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter admin email"
                    value="<?php

                        echo htmlspecialchars(
                            $email ?? ""
                        );

                    ?>"
                    required
                    autocomplete="email"
                >

            </div>

            <div class="form-group">

                <label for="password">

                    Admin Password

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter admin password"
                    required
                    autocomplete="current-password"
                >

            </div>

            <button
                type="submit"
                class="login-button"
            >

                🔐 Secure Admin Login

            </button>

        </form>

        <div class="back">

            <a href="../index.php">

                ← Back to Website

            </a>

        </div>

    </div>

    <div class="security-note">

        🔒 Authorized administrators only.
        Your login is protected by secure
        password verification.

    </div>

</div>

</body>

</html>