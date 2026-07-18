<?php
require_once "../config/db.php";

// Fetch all available music styles
$stylesStmt = $pdo->query("SELECT id, name FROM music_styles ORDER BY name");
$musicStyles = $stylesStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $passwordRaw = trim($_POST["password"]);
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

<form method="POST">
    <input type="text" name="username" placeholder="Username"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="text" name="instrument" placeholder="Instrument (piano, sax...)"><br>
    <input type="text" name="instrument" placeholder="Instrument (piano, sax...)"><br>

    <p><strong>Musical Styles</strong></p>

    <?php foreach ($musicStyles as $style): ?>
        <label>
            <input
                type="checkbox"
                name="music_styles[]"
                value="<?= $style['id']; ?>"
            >
            <?= htmlspecialchars($style['name']); ?>
        </label><br>
    <?php endforeach; ?>

<br>
    <button type="submit">Register</button>
</form>