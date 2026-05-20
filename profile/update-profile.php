<?php

require_once '../middleware/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

$username = trim($_POST['username']);
$email = trim($_POST['email']);

$errors = [];


// Validation


if (empty($username)) {
    $errors[] = "Username is required.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

//Check duplicate email

$sql = "SELECT id FROM users WHERE email = ? AND id != ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$email, $user_id]);

$existingUser = $stmt->fetch();

if ($existingUser) {
    $errors[] = "This email is already used.";
}


//If errors exist


if (!empty($errors)) {

    $_SESSION['errors'] = $errors;

    header("Location: edit-profile.php");

    exit;
}

//Update profile

$sql = "UPDATE users
        SET username = ?, email = ?
        WHERE id = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$username, $email, $user_id]);

$_SESSION['success'] = "Profile updated successfully.";

header("Location: profile.php");

exit;