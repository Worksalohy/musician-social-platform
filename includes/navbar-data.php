<?php

$unreadMessages = 0;
$unreadNotifications = 0;

if (isset($_SESSION['user_id'])) {

    require_once __DIR__ . '/../config/db.php';

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM messages
        WHERE receiver_id = ?
          AND is_read = 0
    ");

    $stmt->execute([$_SESSION['user_id']]);
    $unreadMessages = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM notifications
        WHERE user_id = ?
          AND is_read = 0
    ");

    $stmt->execute([$_SESSION['user_id']]);
    $unreadNotifications = (int)$stmt->fetchColumn();
}

?>