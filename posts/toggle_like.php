<?php

require_once "../middleware/auth.php";
require_once "../config/db.php";

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $post_id = $_POST['post_id'];

    // Check existing like
    $stmt = $pdo->prepare("
        SELECT * FROM likes
        WHERE user_id = ?
        AND post_id = ?
    ");

    $stmt->execute([$user_id, $post_id]);

    $existingLike = $stmt->fetch();

    if ($existingLike) {

        // Unlike
        $stmt = $pdo->prepare("
            DELETE FROM likes
            WHERE user_id = ?
            AND post_id = ?
        ");

        $stmt->execute([$user_id, $post_id]);

    } else {

        // Like
        $stmt = $pdo->prepare("
            INSERT INTO likes (user_id, post_id)
            VALUES (?, ?)
        ");

        $stmt->execute([$user_id, $post_id]);

        // Find the owner of the post
        $stmt = $pdo->prepare("
            SELECT user_id
            FROM posts
            WHERE id = ?
        ");
        $stmt->execute([$post_id]);

        $post_owner = $stmt->fetchColumn();

        if ($post_owner != $user_id) {
            $stmt = $pdo->prepare("
                INSERT INTO notifications
                (user_id, actor_id, post_id, type)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $post_owner,
                $user_id,
                $post_id,
                'like'
            ]);
        }

    }

    header("Location: ../dashboard/dashboard.php");
    exit;
}