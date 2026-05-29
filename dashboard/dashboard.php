<?php
require_once "../middleware/auth.php";
?>

<form action="../posts/create_post.php" method="POST">
    <textarea 
        name="content" 
        placeholder="Share something with musicians..."
        required
    ></textarea>

    <button type="submit">Post</button>
</form>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <title>Dashboard</title>
</head>
<body>

<h1>
    Welcome,
    <?php echo htmlspecialchars($_SESSION["username"]) . " 🎶"; ?>
</h1>

<p>You are logged in.</p>

<a class="profile-link" href="../profile/profile.php">
    My profile
</a>

<a href="../auth/logout.php">
    Logout
</a>

</body>
</html>