<?php
require_once "../config/db.php";
require_once "../middleware/auth.php";

include "../includes/header.php";
?>

<h1>
    Welcome,
    <?php echo htmlspecialchars($_SESSION["username"]) . " 🎶"; ?>
</h1>

<p>You are logged in.</p>

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

// Determine music level
if ($attempts == 0) {
    $musicLevel = "Not Ranked";
} elseif ($bestPercentage < 40) {
    $musicLevel = "Beginner";
} elseif ($bestPercentage < 70) {
    $musicLevel = "Intermediate";
} elseif ($bestPercentage < 90) {
    $musicLevel = "Advanced";
} else {
    $musicLevel = "Expert";
}
?>

<div class="quiz-stats">

    <h2>🎵 Music Quiz</h2>

    <p>
        <strong>Best Score:</strong>
        <?= $bestScore; ?> / <?= $totalQuestions; ?>
    </p>

    <p>
        <strong>Highest Percentage:</strong>
        <?= number_format($bestPercentage, 2); ?>%
    </p>

    <p>
        <strong>Attempts:</strong>
        <?= $attempts; ?>
    </p>

    <p>
        <strong>Music Level:</strong>
        <?= $musicLevel; ?>
    </p>

    <a class="quiz-btn" href="/quiz/index.php">
        Play Quiz
    </a>

</div>

<style>

.quiz-stats{
    background:#fff;
    padding:20px;
    margin:25px 0;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,.1);
}

.quiz-stats h2{
    margin-top:0;
}

.quiz-btn{
    display:inline-block;
    margin-top:15px;
    padding:10px 20px;
    background:#28a745;
    color:#fff;
    text-decoration:none;
    border-radius:5px;
}

.quiz-btn:hover{
    background:#218838;
}

</style>

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