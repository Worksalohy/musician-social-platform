<?php
require_once "middleware/auth.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h1>
    Welcome,
    <?php echo htmlspecialchars($_SESSION["username"]) . " 🎶"; ?>
</h1>

<p>You are logged in.</p>

<a href="auth/logout.php">
    Logout
</a>

</body>
</html>