<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$sender_id = $_SESSION['user_id'];

$receiver_id = $_POST['receiver_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$receiver_id || empty($message)) {
    die("Invalid data.");
}

$stmt = $pdo->prepare("
    INSERT INTO messages (
        sender_id,
        receiver_id,
        message
    )
    VALUES (?, ?, ?)
");

$stmt->execute([
    $sender_id,
    $receiver_id,
    $message
]);

header('Content-Type: application/json');

echo json_encode([
    'success' => true
]);
exit;