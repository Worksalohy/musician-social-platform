<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/message-functions.php";

header('Content-Type: application/json');

$senderId = (int) $_SESSION['user_id'];
$receiverId = (int) ($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if ($receiverId <= 0 || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data.'
    ]);
    exit;
}

$success = sendMessage(
    $pdo,
    $senderId,
    $receiverId,
    $message
);

echo json_encode([
    'success' => $success
]);
exit;