<?php
$pageTitle = "Notifications | MusicCulture";
$currentPage = "notifications";

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/header.php";

// Mark notifications as read
$stmt = $pdo->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = ?
      AND is_read = 0
");

$stmt->execute([$_SESSION['user_id']]);

$user_id = $_SESSION['user_id'];

// Get notifications
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

<style>

.notifications-container{
    max-width:700px;
    margin:auto;
    background:#fff;
    padding:20px;
    border-radius:10px;
}

.notification{
    display:flex;
    align-items:center;
    gap:15px;
    padding:15px;
    border-bottom:1px solid #ddd;
}

.notification.unread{
    background:#f8f9ff;
}

.notification img{
    width:50px;
    height:50px;
    border-radius:50%;
    object-fit:cover;
}

.notification-content{
    flex:1;
}

.notification-time{
    color:#666;
    font-size:13px;
}

</style>

<div class="notifications-container">

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

                <img
                    src="<?= htmlspecialchars($avatar) ?>"
                    alt="Avatar">

                <div class="notification-content">

                    <strong>
                        <?= htmlspecialchars($notification['username']) ?>
                    </strong>

                    <?php if ($notification['type'] === 'like'): ?>

                        liked your post.

                    <?php elseif ($notification['type'] === 'comment'): ?>

                        commented on your post.

                    <?php endif; ?>

                    <br>

                    <span class="notification-time">
                        <?= htmlspecialchars($notification['created_at']) ?>
                    </span>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php require_once "../includes/footer.php"; ?>