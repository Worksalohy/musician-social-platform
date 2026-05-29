<?php

require_once "../middleware/auth.php";
require_once "../config/db.php";

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postId = $_POST['post_id'];

    // Check existing like
    $stmt = $pdo->prepare("
        SELECT * FROM likes
        WHERE user_id = ?
        AND post_id = ?
    ");

    $stmt->execute([$userId, $postId]);

    $existingLike = $stmt->fetch();

    if ($existingLike) {

        // Unlike
        $stmt = $pdo->prepare("
            DELETE FROM likes
            WHERE user_id = ?
            AND post_id = ?
        ");

        $stmt->execute([$userId, $postId]);

    } else {

        // Like
        $stmt = $pdo->prepare("
            INSERT INTO likes (user_id, post_id)
            VALUES (?, ?)
        ");

        $stmt->execute([$userId, $postId]);
    }

    header("Location: ../dashboard/dashboard.php");
    exit;
}