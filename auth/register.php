<?php
require_once "../config/db.php";

// Fetch all available music styles
$stylesStmt = $pdo->query("SELECT id, name FROM music_styles ORDER BY name");
$musicStyles = $stylesStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $passwordRaw = $_POST["password"];
    $instrument = trim($_POST["instrument"]);
    $error = "";
    $password = "";

    if (empty($username) || empty($email) || empty($passwordRaw)) {
        echo "All fields are required.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "Email already exists.";
        exit;
    }

    // Password should not less than 6 lenth.
    if (strlen($passwordRaw) <6) {
        $error = "Password must contain at least 6 characters";
        echo $error;
        exit;
    }else{
        $password = password_hash($passwordRaw, PASSWORD_DEFAULT);
    }

    $stmt = $pdo->prepare("
    INSERT INTO users (username, email, password, instrument)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$username, $email, $password, $instrument]);

// Get the ID of the newly created user
$userId = $pdo->lastInsertId();

// Save selected music styles
if (!empty($_POST['music_styles'])) {

    $styleStmt = $pdo->prepare("
        INSERT INTO user_music_styles (user_id, style_id)
        VALUES (?, ?)
    ");

    foreach ($_POST['music_styles'] as $styleId) {
        $styleStmt->execute([$userId, $styleId]);
    }
}

header("Location: login.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/auth.css">

    <title>Create account | MusicCulture</title>
</head>
<body>

    <main class="auth-card auth-card-register">

        <p class="auth-brand">MUSICCULTURE</p>

        <h1>Create your account</h1>

        <p class="auth-intro">
            Join MusicCulture and connect with other musicians.
        </p>

        <form method="POST" class="auth-form">

            <div class="form-group">
                <label for="username">Username</label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Choose a username"
                    autocomplete="username"
                    required
                >
            </div>

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
                    placeholder="At least 6 characters"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="form-group">
                <label for="instrument">Instrument</label>

                <input
                    type="text"
                    id="instrument"
                    name="instrument"
                    placeholder="e.g. Piano, Saxophone, Guitar"
                >
            </div>

            <fieldset class="music-styles">
                <legend>Musical Styles</legend>

                <p class="styles-intro">
                    Select the styles you enjoy playing.
                </p>

                <div class="styles-grid">
                    <?php foreach ($musicStyles as $style): ?>
                        <label class="style-option">
                            <input
                                type="checkbox"
                                name="music_styles[]"
                                value="<?= $style['id']; ?>"
                            >
                            <span>
                                <?= htmlspecialchars($style['name']); ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <button type="submit" class="auth-button">
                Create account
            </button>

        </form>

        <p class="auth-footer">
            Already have an account?
            <a href="login.php">Log in</a>
        </p>

    </main>

</body>
</html>
