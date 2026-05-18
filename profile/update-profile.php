<?php
require_once '../middleware/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username']);
$email = trim($_POST['email']);

$sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$username, $email, $user_id]);

header("Location: profile.php");
exit;
?>