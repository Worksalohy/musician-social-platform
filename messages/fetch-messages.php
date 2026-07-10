<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'] ?? null;

if (!$other_user_id) {
    exit;
}

// Mark all messages from the other user as read
$stmt = $pdo->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = ?
      AND receiver_id = ?
      AND is_read = 0
");

$stmt->execute([
    $other_user_id,
    $current_user_id
]);

$stmt = $pdo->prepare("
    SELECT m.*, u.username
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE
        (m.sender_id = ? AND m.receiver_id = ?)
        OR
        (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
");

$stmt->execute([
    $current_user_id,
    $other_user_id,
    $other_user_id,
    $current_user_id
]);

$messages = $stmt->fetchAll();

foreach ($messages as $message):
?>

<div class="message">
    <strong>
        <?= $message['sender_id'] == $current_user_id ? "You" : htmlspecialchars($message['username']) ?>
    </strong>

    <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>

    <small><?= $message['created_at'] ?></small>
</div>

<?php endforeach; ?>
