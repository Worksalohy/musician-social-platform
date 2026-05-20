<?php

require_once 'middleware/auth.php';
require_once 'config/db.php';

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {

        $file = $_FILES['avatar'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($file['type'], $allowedTypes)) {
            die('Invalid file type.');
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            die('File is too large.');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $newFileName = uniqid() . '.' . $extension;

        $uploadPath = 'uploads/avatars/' . $newFileName;

       if (move_uploaded_file($file['tmp_name'], $uploadPath)) {

    // Get current avatar
        $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete old avatar if it exists
        if (!empty($currentUser['avatar']) && file_exists($currentUser['avatar'])) {

            unlink($currentUser['avatar']);
        }

    // Update database
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");

        $stmt->execute([$uploadPath, $userId]);

        header('Location: /profile/profile.php');
        exit;

        } else {
            echo "Upload failed.";
        }

    } else {
        echo "No file uploaded.";
    }

}