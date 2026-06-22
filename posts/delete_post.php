<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Post ID missing'
    ]);
    exit;
}

// Verify ownership
$stmt = $pdo->prepare("
    SELECT user_id
    FROM posts
    WHERE id = ?
");

$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo json_encode([
        'success' => false,
        'message' => 'Post not found'
    ]);
    exit;
}

if ($post['user_id'] != $user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Delete post
$stmt = $pdo->prepare("
    DELETE FROM posts
    WHERE id = ?
");

$stmt->execute([$post_id]);

echo json_encode([
    'success' => true
]);