<?php
$pageTitle = "Profile | MusicCulture";

$pageStyles = [
    "/assets/css/profile.css"
];

$pageScripts = [
    "/assets/js/profile.js"
];

$currentPage = "profile";

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/header.php";

// Get logged-in user ID
$profile_user_id = isset($_GET['id'])
    ? (int) $_GET['id']
    : $_SESSION['user_id'];

$isOwnProfile = ($profile_user_id === (int) $_SESSION['user_id']);
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
$stmt = $pdo->prepare("
    SELECT 1
    FROM follows
    WHERE follower_id = ?
      AND following_id = ?
");

$stmt->execute([
    $_SESSION['user_id'],
    $profile_user_id
]);

$isFollowing = (bool) $stmt->fetchColumn();

// Avatar
$avatar = !empty($user['avatar'])
    ? "../" . $user['avatar']
    : "../assets/musicculture-default-avatar.png";
?>

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

<?php require_once "../includes/footer.php"; ?>