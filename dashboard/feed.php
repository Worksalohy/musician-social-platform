<?php
require_once "../config/db.php";

$sql = "SELECT posts.id,
               posts.user_id,
               posts.content,
               posts.created_at,
               users.username,
               users.avatar
        FROM posts
        JOIN users
        ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";

$stmt = $pdo->query($sql);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php foreach ($posts as $post): ?>

    <div class="post">

        <?php if (!empty($post["avatar"])): ?>

            <img
                src="../<?= htmlspecialchars($post["avatar"]) ?>"
                alt="Avatar"
                width="50"
            >

        <?php else: ?>

            <img
                src="../assets/musicculture-default-avatar.png"
                alt="Default Avatar"
                width="50"
            >

        <?php endif; ?>

        <h3>
            <?= htmlspecialchars($post["username"]) ?>
        </h3>

        <p>
            <?= nl2br(htmlspecialchars($post["content"])) ?>
        </p>

        <small>
            <?= htmlspecialchars($post["created_at"]) ?>
        </small>

    </div>

    <?php if ($_SESSION['user_id'] === $post['user_id']): ?>

        <form action="../posts/delete_post.php" method="POST" style="display:inline;">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <button type="submit" onclick="return confirm('Delete this post?')">
                Delete
            </button>
        </form>

    <?php endif; ?>

    <?php if ($_SESSION['user_id'] === $post['user_id']): ?>

        <a href="../posts/edit_post.php?id=<?= $post['id'] ?>">
            Edit
        </a>

    <?php endif; ?>

    <hr>

<?php endforeach; ?>

