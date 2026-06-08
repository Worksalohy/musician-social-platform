<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$user_id = $_SESSION["user_id"];

$post_id = $_POST["post_id"] ?? null;
$content = trim($_POST["content"] ?? "");

if (!$post_id || empty($content)) {
    header("Location: ../dashboard/dashboard.php");
    exit;
}

$sql = "INSERT INTO comments (post_id, user_id, content)
        VALUES (:post_id, :user_id, :content)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "post_id" => $post_id,
    "user_id" => $user_id,
    "content" => $content
]);

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
        'comment'
    ]);
}

header('Content-Type: application/json');

echo json_encode([
    'success' => true
]);

exit;