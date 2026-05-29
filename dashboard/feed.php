<?php
require_once "../config/db.php";

$sql = "SELECT posts.content, posts.created_at, users.username
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";

$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($posts as $post): ?>
    <div class="post">
        <h4><?= htmlspecialchars($post['username']) ?></h4>

        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

        <small>
            <?= htmlspecialchars($post['created_at']) ?>
        </small>
    </div>
<?php endforeach; ?>