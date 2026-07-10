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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages</title>

<style>

body {
    font-family: Arial, sans-serif;
    background:#f4f4f4;
    padding:40px;
}

.container{
    max-width:700px;
    margin:auto;
    background:#fff;
    padding:20px;
    border-radius:10px;
}

.conversation{
    display:flex;
    align-items:center;
    gap:15px;
    padding:15px;
    border-bottom:1px solid #ddd;
    text-decoration:none;
    color:#000;
}

.conversation:hover{
    background:#f8f8f8;
}

.avatar{
    width:50px;
    height:50px;
    border-radius:50%;
    object-fit:cover;
}

.username{
    font-weight:bold;
}

.unread-badge{
    background:red;
    color:white;
    border-radius:50%;
    min-width:24px;
    height:24px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:12px;
    margin-left:auto;
}

</style>

</head>
<body>

<div class="container">

    <h2>Messages</h2>

    <?php if (empty($conversations)): ?>

    <p>No conversations yet.</p>

    <?php else: ?>

    <?php foreach ($conversations as $conversation): ?>

    <?php
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

<?php endif; ?>

</div>

</body>
</html>