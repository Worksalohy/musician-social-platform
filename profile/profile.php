<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

// Get logged-in user ID
$user_id = $_SESSION["user_id"];

// Fetch user information
$sql = "SELECT username, email, instrument, created_at 
        FROM users 
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(["id" => $user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user not found
if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }

        .profile-card {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: crimson;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background: darkred;
        }
    </style>
</head>
<body>

<div class="profile-card">
    <h1>My Profile</h1>

    <p><strong>Username:</strong> <?= htmlspecialchars($user["username"]) ?></p>

    <p><strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?></p>

    <p><strong>Instrument:</strong> <?= htmlspecialchars($user["instrument"]) ?></p>

    <p><strong>Member since:</strong> <?= htmlspecialchars($user["created_at"]) ?></p>

    <a class="logout-btn" href="../auth/logout.php">Logout</a>
</div>

<a href="edit-profile.php">Edit Profile</a>

</body>
</html>