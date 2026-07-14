<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

// Get logged-in user ID
$profile_user_id = $_GET['id'] ?? $_SESSION['user_id'];
$isOwnProfile = ($profile_user_id == $_SESSION['user_id']);

// Fetch user information
$sql = "SELECT id, username, email, instrument, created_at, avatar 
        FROM users 
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(["id" => $profile_user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user not found
if (!$user) {
    die("User not found.");
}
?>

<?php
$isFollowing = false;

$stmt = $pdo->prepare("
    SELECT follower_id
    FROM follows
    WHERE follower_id = ? AND following_id = ?
");

$stmt->execute([$_SESSION['user_id'], $profile_user_id]);

if ($stmt->fetch()) {
    $isFollowing = true;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/musicculture-default-avatar.png">
    <title>Profile</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }

        .profile-card {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
        }

        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: crimson;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background: darkred;
        }
    </style>
</head>
<body>

<div class="profile-card">
    <h1>
    <?= $isOwnProfile
        ? 'My Profile'
        : htmlspecialchars($user['username']) . "'s Profile" ?>
    </h1>

    <p><strong>Username:</strong> <?= htmlspecialchars($user["username"]) ?></p>

    <p><strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?></p>

    <p><strong>Instrument:</strong> <?= htmlspecialchars($user["instrument"]) ?></p>

    <p><strong>Member since:</strong> <?= htmlspecialchars($user["created_at"]) ?></p>

</div>

<div>
    <form action="../upload-avatar.php" method="POST" enctype="multipart/form-data">

        <label>Select Avatar:</label><br>
    
        <input type="file" name="avatar" accept="image/*" required>

        <button type="submit">Upload Avatar</button>

    </form>
</div>

<?php if ($isOwnProfile): ?>

    <a class="logout-btn" href="../auth/logout.php">Logout</a>

    <a href="edit-profile.php">Edit Profile</a>

<?php endif; ?>

<?php
$avatar = "../assets/musicculture-default-avatar.png";

if (!empty($user['avatar'])) {
    $avatar = "../" . $user['avatar'];
}
?>

<img src="<?= htmlspecialchars($avatar) ?>" alt="Profile Avatar" width="120">

<?php if ($_SESSION['user_id'] != $user['id']): ?>

    <button id="follow-btn" data-user-id="<?= $user['id'] ?>">
        <?= $isFollowing ? "Unfollow" : "Follow" ?>
    </button>

    <a href="../messages/chat.php?user_id=<?= $user['id'] ?>">
        <button type="button">Message</button>
    </a>

<?php endif; ?>

<script>
    document.getElementById("follow-btn")?.addEventListener("click", function () {

    const userId = this.dataset.userId;

    fetch("../users/follow-toggle.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "user_id=" + encodeURIComponent(userId)
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            this.textContent =
                data.action === "follow" ? "Unfollow" : "Follow";
        }

    });
});

function loadUnreadMessages() {
    fetch('/messages/get-unread-message.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) return;

            const badge = document.getElementById('messageBadge');

            if (data.unread_count > 0) {
                badge.textContent = data.unread_count;
                badge.style.display = 'inline-block';
            } else {
                badge.textContent = '';
                badge.style.display = 'none';
            }
        })
        .catch(error => console.error(error));
}

// Load immediately
loadUnreadMessages();

// Check every 3 seconds
setInterval(loadUnreadMessages, 3000);

</script>

<a href="../messages/inbox.php">
    Messages
    <span id="messageBadge" style="display:none;"></span>
</a>

</body>
</html>