<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) AS unread_count
    FROM messages
    WHERE receiver_id = ?
      AND is_read = 0
");
$stmt->execute([$user_id]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "unread_count" => (int)$result["unread_count"]
]);