<?php
require_once '../middleware/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

$user = $stmt->fetch();

// Fetch all available musical styles
$stmt = $pdo->query("
    SELECT id, name
    FROM music_styles
    ORDER BY name
");

$allStyles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the user's selected styles
$stmt = $pdo->prepare("
    SELECT style_id
    FROM user_music_styles
    WHERE user_id = ?
");

$stmt->execute([$user_id]);

$userStyles = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<?php if (!empty($_SESSION['success'])): ?>

    <p style="color:green;">
        <?php echo $_SESSION['success']; ?>
    </p>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>

    <?php foreach ($_SESSION['errors'] as $error): ?>

        <p style="color:red;">
            <?php echo $error; ?>
        </p>

    <?php endforeach; ?>

    <?php unset($_SESSION['errors']); ?>

<?php endif; ?>



<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>

<h1>Edit Profile</h1>

<form action="update-profile.php" method="POST">

    <label>Username:</label><br>
    <input type="text" name="username"
           value="<?php echo htmlspecialchars($user['username']); ?>">
    <br><br>

    <label>Email:</label><br>
    <input type="email" name="email"
           value="<?php echo htmlspecialchars($user['email']); ?>">
    <br><br>

    <label>Musical Styles:</label><br><br>

    <?php foreach ($allStyles as $style): ?>

        <label>

            <input
                type="checkbox"
                name="music_styles[]"
                value="<?= $style['id']; ?>"

                <?= in_array($style['id'], $userStyles) ? 'checked' : ''; ?>

            >

            <?= htmlspecialchars($style['name']); ?>

        </label>

        <br>

    <?php endforeach; ?>

    <br>

    <button type="submit">Update Profile</button>

</form>

</body>
</html>