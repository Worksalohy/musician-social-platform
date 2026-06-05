<?php

require_once "../middleware/auth.php";
require_once "../config/db.php";

$stmt = $pdo->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = ?
    AND is_read = 0
");

$stmt->execute([$_SESSION['user_id']]);


$user_id = $_SESSION['user_id'];

$sql = "
    SELECT
        notifications.*,
        users.username,
        users.avatar
    FROM notifications
    JOIN users
        ON notifications.actor_id = users.id
    WHERE notifications.user_id = ?
    ORDER BY notifications.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

$notifications = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification | Music Culture</title>
</head>

<body>
    <h2>Notifications</h2>

<?php if (empty($notifications)): ?>

    <p>No notifications yet.</p>

<?php else: ?>

    <?php foreach ($notifications as $notification): ?>

        <?php
            $avatar = !empty($notification['avatar'])
            ? "../" . $notification['avatar']
            : "../assets/musicculture-default-avatar.png";
        ?>

        <div class="<?= $notification['is_read'] ? 'notification' : 'notification unread' ?>">

            <?php if ($avatar): ?>
                <img
                    src="<?= htmlspecialchars($avatar) ?>"
                    alt="Avatar"
                    width="40"
                    height="40"
                >
            <?php endif; ?>

            <strong>
                <?= htmlspecialchars($notification['username']) ?>
            </strong>

            <?php if ($notification['type'] === 'like'): ?>

                liked your post.

            <?php elseif ($notification['type'] === 'comment'): ?>

                commented on your post.

            <?php endif; ?>

            <br>

            <small>
                <?= $notification['created_at'] ?>
            </small>

        </div>

        <hr>

    <?php endforeach; ?>

<?php endif; ?>

<strong>
    <a href="../dashboard/dashboard.php">Dashboard</a>
</strong>
</body>
</html>