<?php
session_start();
require_once "../config/db.php";
require_once "../middleware/auth.php";

header('Content-Type: application/json');

$comment_id = $_POST["comment_id"] ?? null;
$user_id = $_SESSION["user_id"];

if (!$comment_id) {
    echo json_encode(['success' => false]);
    exit;
}

$sql = "DELETE FROM comments
        WHERE id = :id
        AND user_id = :user_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "id" => $comment_id,
    "user_id" => $user_id
]);

echo json_encode([
    'success' => $stmt->rowCount() > 0
]);

exit;