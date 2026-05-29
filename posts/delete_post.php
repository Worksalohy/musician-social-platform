<?php

require_once "../middleware/auth.php";
require_once "../config/db.php";

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postId = $_POST['post_id'];

    // Check ownership
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Post not found.");
    }

    if ($post['user_id'] != $userId) {
        die("Unauthorized action.");
    }

    // Delete post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$postId]);

    header("Location: ../dashboard/dashboard.php");
    exit;
}