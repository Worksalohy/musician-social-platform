<?php
$pageTitle = "Dashboard | MusicCulture";
$currentPage = "dashboard";

$pageStyles = [
    "/assets/css/dashboard.css",
    "/assets/css/feed.css"
];

require_once "../config/db.php";
require_once "../middleware/auth.php";

include "../includes/header.php";
?>

<div class="dashboard-header">

    <h1>
        👋 Welcome back,
        <?= htmlspecialchars($_SESSION["username"]); ?>
    </h1>

    <p>
        Connect with musicians, share your passion,
        and improve your music knowledge.
    </p>

</div>

<?php
// Get unread notifications
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM notifications
    WHERE user_id = ?
    AND is_read = 0
");

$stmt->execute([$_SESSION['user_id']]);
$unreadCount = $stmt->fetchColumn();

// Get user's best quiz result
$stmt = $pdo->prepare("
    SELECT
        score,
        total_questions,
        percentage
    FROM quiz_results
    WHERE user_id = ?
    ORDER BY percentage DESC, completed_at DESC
    LIMIT 1
");

$stmt->execute([$_SESSION['user_id']]);
$bestResult = $stmt->fetch(PDO::FETCH_ASSOC);

// Get total number of quiz attempts
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS attempts
    FROM quiz_results
    WHERE user_id = ?
");

$stmt->execute([$_SESSION['user_id']]);
$attempts = $stmt->fetch(PDO::FETCH_ASSOC)['attempts'];

// Default values
if ($bestResult) {
    $bestScore = $bestResult['score'];
    $totalQuestions = $bestResult['total_questions'];
    $bestPercentage = $bestResult['percentage'];
} else {
    $bestScore = 0;
    $totalQuestions = 0;
    $bestPercentage = 0;
}

// Get the user's music level
$stmt = $pdo->prepare("
    SELECT music_level
    FROM users
    WHERE id = ?
");

$stmt->execute([$_SESSION['user_id']]);
$musicLevel = $stmt->fetchColumn();
?>

<div class="stats-grid">

    <div class="stat-card">

        <h3>🎵 Music Level</h3>

        <p><?= htmlspecialchars($musicLevel); ?></p>

    </div>

    <div class="stat-card">

        <h3>🏆 Best Score</h3>

        <p><?= $bestScore ?> / <?= $totalQuestions ?></p>

    </div>

    <div class="stat-card">

        <h3>📈 Highest Score</h3>

        <p><?= number_format($bestPercentage,1) ?>%</p>

    </div>

    <div class="stat-card">

        <h3>🎮 Attempts</h3>

        <p><?= $attempts ?></p>

    </div>

</div>

<div class="quiz-banner">

    <h2>🎮 Ready for another challenge?</h2>

    <p>

        Play the Music Quiz again to improve your score
        and earn a higher Music Level.

    </p>

    <a class="btn" href="/quiz/index.php">

        Play Quiz

    </a>

</div>



<a href="../notifications/notifications.php">
    Notifications
    <?php if ($unreadCount > 0): ?>
        (<?= $unreadCount ?>)
    <?php endif; ?>
</a>

<a class="profile-link" href="../profile/profile.php">
    My profile
</a>

<a href="../auth/logout.php">
    Logout
</a>

<!-- Create post form -->
<form action="../posts/create_post.php" method="POST">
    <textarea
        name="content"
        placeholder="Share something with musicians..."
        required
    ></textarea>

    <button type="submit">Post</button>
</form>

<hr>

<!-- Feed -->
<?php require_once "feed.php"; ?>

<!-- JavaScript -->
<script>
document.querySelectorAll('.like-form').forEach(form => {

    form.addEventListener('submit', async function(e) {

        e.preventDefault();

        const formData = new FormData(this);

        const response = await fetch(
            '../posts/toggle_like.php',
            {
                method: 'POST',
                body: formData
            }
        );

        const data = await response.json();

        const button = this.querySelector('.like-button');

        const likeCount = this.parentElement.querySelector('.like-count');

        button.textContent = data.liked ? 'Unlike' : 'Like';

        likeCount.textContent = data.count + ' likes';

    });

});
</script>

<?php include "../includes/footer.php"; ?>