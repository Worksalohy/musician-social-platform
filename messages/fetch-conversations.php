<?php

session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/message-functions.php";


$user_id = $_SESSION['user_id'];


$conversations = getUserConversations(
    $pdo,
    $user_id
);


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