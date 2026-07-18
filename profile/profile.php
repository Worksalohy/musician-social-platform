<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/header.php";

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

// Fetch user's musical styles
$stmt = $pdo->prepare("
    SELECT ms.name
    FROM user_music_styles ums
    INNER JOIN music_styles ms
        ON ums.style_id = ms.id
    WHERE ums.user_id = ?
    ORDER BY ms.name
");

$stmt->execute([$profile_user_id]);

$musicStyles = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Check if current user follows this profile
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

// Avatar
$avatar = "../assets/musicculture-default-avatar.png";

if (!empty($user['avatar'])) {
    $avatar = "../" . $user['avatar'];
}
?>

<style>

.profile-card{
    max-width:500px;
    margin:auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

.profile-card h1{
    margin-bottom:20px;
}

.profile-card p{
    margin:10px 0;
}

.logout-btn{
    display:inline-block;
    margin-top:20px;
    padding:10px 15px;
    background:crimson;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.logout-btn:hover{
    background:darkred;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    margin:20px 0;
}

.actions{
    margin-top:20px;
}

.actions a,
.actions button{
    padding:10px 15px;
    margin-right:10px;
    cursor:pointer;
}

.upload-avatar{
    margin-top:20px;
}

.music-styles{
    margin:10px 0;
}

.style-badge{
    display:inline-block;
    padding:6px 12px;
    margin:4px 4px 0 0;
    background:#4CAF50;
    color:white;
    border-radius:20px;
    font-size:14px;
    font-weight:500;
}

</style>

<div class="profile-card">

    <h1>
        <?= $isOwnProfile
            ? "My Profile"
            : htmlspecialchars($user['username']) . "'s Profile" ?>
    </h1>

    <img
        class="avatar"
        src="<?= htmlspecialchars($avatar) ?>"
        alt="Profile Avatar">

    <p>
        <strong>Username:</strong>
        <?= htmlspecialchars($user["username"]) ?>
    </p>

    <p>
        <strong>Email:</strong>
        <?= htmlspecialchars($user["email"]) ?>
    </p>

    <p>
        <strong>Instrument:</strong>
        <?= htmlspecialchars($user["instrument"]) ?>
    </p>

    <div class="music-styles">

        <strong>Musical Styles:</strong><br><br>

        <?php if (!empty($musicStyles)): ?>

            <?php foreach ($musicStyles as $style): ?>

                <span class="style-badge">
                    🎵 <?= htmlspecialchars($style) ?>
                </span>

            <?php endforeach; ?>

        <?php else: ?>

            <em>No musical styles selected.</em>

        <?php endif; ?>

    </div>

    <p>
        <strong>Member since:</strong>
        <?= htmlspecialchars($user["created_at"]) ?>
    </p>

    <?php if ($isOwnProfile): ?>

        <div class="upload-avatar">

            <form
                action="../upload-avatar.php"
                method="POST"
                enctype="multipart/form-data">

                <label>Select Avatar:</label><br><br>

                <input
                    type="file"
                    name="avatar"
                    accept="image/*"
                    required>

                <button type="submit">
                    Upload Avatar
                </button>

            </form>

        </div>

        <div class="actions">

            <a class="logout-btn" href="../auth/logout.php">
                Logout
            </a>

            <a href="edit-profile.php">
                <button type="button">
                    Edit Profile
                </button>
            </a>

        </div>

    <?php else: ?>

        <div class="actions">

            <button
                id="follow-btn"
                data-user-id="<?= $user['id'] ?>">

                <?= $isFollowing ? "Unfollow" : "Follow" ?>

            </button>

            <a href="../messages/chat.php?user_id=<?= $user['id'] ?>">

                <button type="button">
                    Message
                </button>

            </a>

        </div>

    <?php endif; ?>

</div>

<script>

document.getElementById("follow-btn")?.addEventListener("click", function(){

    const userId = this.dataset.userId;

    fetch("../users/follow-toggle.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:"user_id="+encodeURIComponent(userId)
    })
    .then(res=>res.json())
    .then(data=>{

        if(data.success){

            this.textContent =
                data.action==="follow"
                ? "Unfollow"
                : "Follow";

        }

    });

});

</script>

<?php require_once "../includes/footer.php"; ?>