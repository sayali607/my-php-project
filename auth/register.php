```php
<?php

require_once "../config/db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        $name === "" ||
        $email === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $message = "Please fill all fields.";
        $message_type = "error";

    } elseif (strlen($name) < 2) {

        $message = "Please enter a valid name.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } elseif (strlen($password) < 8) {

        $message =
            "Password must be at least 8 characters.";

        $message_type = "error";

    } else {

        $check = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        if (!$check) {

            $message =
                "Something went wrong. Please try again.";

            $message_type = "error";

        } else {

            $check->bind_param(
                "s",
                $email
            );

            $check->execute();

            $result = $check->get_result();

            if (
                $result &&
                $result->num_rows > 0
            ) {

                $message =
                    "This email is already registered.";

                $message_type =
                    "error";

            } else {

                $hashed_password =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                $stmt = $conn->prepare(
                    "INSERT INTO users
                        (name, email, password)
                     VALUES
                        (?, ?, ?)"
                );

                if (!$stmt) {

                    $message =
                        "Unable to create your account.";

                    $message_type =
                        "error";

                } else {

                    $stmt->bind_param(
                        "sss",
                        $name,
                        $email,
                        $hashed_password
                    );

                    if ($stmt->execute()) {

                        $message =
                            "Registration successful! You can now login.";

                        $message_type =
                            "success";

                        $name = "";
                        $email = "";

                    } else {

                        $message =
                            "Registration failed. Please try again.";

                        $message_type =
                            "error";
                    }

                    $stmt->close();

                }

            }

            $check->close();

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
        Create Account - ExploreWorld
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

        .register-card {

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

        .register-card h1 {

            color: #102a43;

            font-size: 30px;

            margin-bottom: 9px;

        }

        .subtitle {

            color: #64748b;

            line-height: 1.6;

            font-size: 14px;

            margin-bottom: 27px;

        }

        .error-message {

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

        .success-message {

            background: #f0fdf4;

            border:
                1px solid
                #bbf7d0;

            color: #15803d;

            padding: 13px 14px;

            border-radius: 11px;

            margin-bottom: 20px;

            font-size: 13px;

            line-height: 1.5;

        }

        .form-group {

            margin-bottom: 18px;

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

        .password-note {

            margin-top: 6px;

            color: #94a3b8;

            font-size: 11px;

        }

        .register-button {

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

        .register-button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(37, 99, 235, 0.25);

        }

        .register-button:active {

            transform: translateY(0);

        }

        .login-section {

            text-align: center;

            margin-top: 23px;

            padding-top: 20px;

            border-top:
                1px solid
                #e2e8f0;

            color: #64748b;

            font-size: 14px;

        }

        .login-section a {

            color: #2563eb;

            text-decoration: none;

            font-weight: 700;

            margin-left: 4px;

        }

        .login-section a:hover {

            color: #1d4ed8;

            text-decoration: underline;

        }

        .home-link {

            display: block;

            text-align: center;

            margin-top: 18px;

            color: #2563eb;

            text-decoration: none;

            font-size: 14px;

            font-weight: 700;

        }

        .home-link:hover {

            color: #1d4ed8;

            text-decoration: underline;

        }

        .security-note {

            text-align: center;

            margin-top: 15px;

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

            .register-card {

                padding: 28px 22px;

                border-radius: 20px;

            }

            .register-card h1 {

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

    <div class="register-card">

        <div class="badge-wrapper">

            <div class="badge">

                ✈️

                JOIN EXPLOREWORLD

            </div>

        </div>

        <h1>

            Create Account

        </h1>

        <p class="subtitle">

            Create your account and start
            discovering amazing destinations,
            reviews and travel experiences.

        </p>

        <?php if ($message !== ""): ?>

            <div
                class="<?php

                    echo $message_type === "success"
                        ? "success-message"
                        : "error-message";

                ?>"
            >

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

                <label for="name">

                    Full Name

                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php

                        echo htmlspecialchars(
                            $name ?? ""
                        );

                    ?>"
                    required
                    autocomplete="name"
                >

            </div>

            <div class="form-group">

                <label for="email">

                    Email Address

                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
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

                    Password

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    required
                    autocomplete="new-password"
                >

                <div class="password-note">

                    Password must contain at least
                    8 characters.

                </div>

            </div>

            <div class="form-group">

                <label for="confirm_password">

                    Confirm Password

                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Enter password again"
                    required
                    autocomplete="new-password"
                >

            </div>

            <button
                type="submit"
                class="register-button"
            >

                ✈️ Create My Account

            </button>

        </form>

        <div class="login-section">

            Already have an account?

            <a href="login.php">

                Login

            </a>

        </div>

    </div>

    <a
        href="../index.php"
        class="home-link"
    >

        ← Back to Website

    </a>

    <div class="security-note">

        🔒 Your password is securely hashed
        before being stored.

    </div>

</div>

</body>

</html>
