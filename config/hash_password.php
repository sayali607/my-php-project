<?php

// =====================================================
// ADMIN PASSWORD HASH GENERATOR
// =====================================================

$admins = [

    [
        "name" => "Sayali",
        "email" => "sayali@touristrecommendation.com",
        "password" => "Sayali@2026#Admin"
    ],

    [
        "name" => "Bhumika",
        "email" => "bhumika@touristrecommendation.com",
        "password" => "Bhumika@2026#Admin"
    ],

    [
        "name" => "Neha",
        "email" => "neha@touristrecommendation.com",
        "password" => "Neha@2026#Admin"
    ]

];


// =====================================================
// GENERATE HASHES
// =====================================================

foreach ($admins as $admin) {

    echo "<h3>";
    echo htmlspecialchars($admin["name"]);
    echo "</h3>";

    echo "Email: ";
    echo htmlspecialchars($admin["email"]);

    echo "<br><br>";

    echo "<strong>Password Hash:</strong>";

    echo "<br>";

    echo password_hash(
        $admin["password"],
        PASSWORD_DEFAULT
    );

    echo "<hr>";
}

?>