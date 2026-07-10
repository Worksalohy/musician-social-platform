<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$user_id = $_SESSION['user_id'];

// Get all users that have chatted with the logged-in user
$stmt = $pdo->prepare("
    SELECT
        u.id,
        u.username,
        u.avatar,

        (
            SELECT COUNT(*)
            FROM messages
            WHERE sender_id = u.id
              AND receiver_id = ?
              AND is_read = 0
        ) AS unread_count

    FROM users u

    JOIN messages m
        ON (
            (m.sender_id = u.id AND m.receiver_id = ?)
            OR
            (m.receiver_id = u.id AND m.sender_id = ?)
        )

    GROUP BY u.id, u.username, u.avatar

    ORDER BY
        unread_count DESC,
        u.username ASC
");

$stmt->execute([
    $user_id,
    $user_id,
    $user_id
]);

$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($conversations)) {
    echo "<p>No conversations yet.</p>";
    exit;
}

foreach ($conversations as $conversation):

    $avatar = "../assets/musicculture-default-avatar.png";

    if (!empty($conversation['avatar'])) {
        $avatar = "../" . $conversation['avatar'];
    }
?>

<a class="conversation"
   href="chat.php?user_id=<?= $conversation['id'] ?>">

    <img
        class="avatar"
        src="<?= htmlspecialchars($avatar) ?>"
        alt="Avatar">

    <div>
        <div class="username">
            <?= htmlspecialchars($conversation['username']) ?>
        </div>
    </div>

    <?php if ($conversation['unread_count'] > 0): ?>
        <div class="unread-badge">
            <?= $conversation['unread_count'] ?>
        </div>
    <?php endif; ?>

</a>

<?php endforeach; ?>