<?php
session_start();
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        header("Location: ../dashboard/dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/auth.css">

    <title>Log in | MusicCulture</title>
</head>
<body>

    <main class="auth-card">

        <p class="auth-brand">MUSICCULTURE</p>

        <h1>Welcome back</h1>

        <p class="auth-intro">
            Log in to your MusicCulture account.
        </p>

        <?php if (isset($error)): ?>
            <p class="auth-error" role="alert">
                <?= htmlspecialchars($error); ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="auth-form">

            <div class="form-group">
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="auth-button">
                Log in
            </button>

        </form>

        <p class="auth-footer">
            Don't have an account?
            <a href="register.php">Create one</a>
        </p>

    </main>

</body>
</html>
