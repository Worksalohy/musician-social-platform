<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?? "MusicCulture"; ?></title>

    <link rel="stylesheet" href="/assets/css/main.css">

    <?php
    if (!empty($pageStyles)) {
        foreach ($pageStyles as $style) {
            echo '<link rel="stylesheet" href="' .
                htmlspecialchars($style) .
                '">' . PHP_EOL;
        }
    }
    ?>

</head>

<body>

<?php

if (isset($_SESSION['user_id'])) {

    require_once __DIR__ . "/../config/db.php";


    // Unread messages count
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM messages
        WHERE receiver_id = ?
        AND is_read = 0
    ");

    $stmt->execute([$_SESSION['user_id']]);

    $unreadMessages = $stmt->fetchColumn();



    // Unread notifications count
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM notifications
        WHERE user_id = ?
        AND is_read = 0
    ");

    $stmt->execute([$_SESSION['user_id']]);

    $unreadNotifications = $stmt->fetchColumn();

}

require_once __DIR__ . "/navbar.php";

?>

<div class="container">