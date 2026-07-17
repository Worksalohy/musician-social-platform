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
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM notifications
    WHERE user_id = ?
    AND is_read = 0
");

$stmt->execute([$_SESSION['user_id']]);

$unreadCount = $stmt->fetchColumn();
?>

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
    
    <!-- Javascript -->
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

        const button =
            this.querySelector('.like-button');

        const likeCount =
            this.parentElement.querySelector('.like-count');

            button.textContent =
                data.liked ? 'Unlike' : 'Like';

            likeCount.textContent =
                data.count + ' likes';

        });

    });
    </script>
<?php include "../includes/footer.php"; ?>

