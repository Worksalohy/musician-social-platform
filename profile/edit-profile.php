<?php
require_once '../middleware/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

$user = $stmt->fetch();
?>

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

    <button type="submit">Update Profile</button>

</form>

</body>
</html>