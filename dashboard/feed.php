<?php
require_once "../config/db.php";
$user_id = $_SESSION['user_id'];

$sql = "SELECT posts.id,
               posts.user_id,
               posts.content,
               posts.created_at,
               users.username,
               users.avatar,
               (
                   SELECT COUNT(*)
                   FROM likes
                   WHERE likes.post_id = posts.id
               ) AS like_count,
               (
                    SELECT COUNT(*)
                    FROM likes
                    WHERE likes.post_id = posts.id
                    AND likes.user_id = :user_id
               )AS liked_by_user,
               (
                    SELECT COUNT(*)
                    FROM comments
                    WHERE comments.post_id = posts.id
               ) AS comment_count
        FROM posts
        JOIN users
        ON posts.user_id = users.id
        ORDER BY posts.created_at DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "user_id" => $user_id
]);

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

    <form action="../posts/toggle_like.php" method="POST">

        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

        <button type="submit">
            <?php if ($post['liked_by_user']): ?>
                Unlike
            <?php else: ?>
                Like
            <?php endif; ?>
        </button>

    </form>

    <!-- About comment -->
        <?php
            $sql = "SELECT comments.id,
                       comments.user_id,
                       comments.content,
                       comments.created_at,
                       users.username,
                       users.avatar
                FROM comments
                JOIN users
                ON comments.user_id = users.id
                WHERE comments.post_id = :post_id
                ORDER BY comments.created_at ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "post_id" => $post['id']
            ]);

            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

    <form action="../comments/create_comment.php" method="POST">

    <input
        type="hidden"
        name="post_id"
        value="<?= $post['id'] ?>"
    >

    <textarea
        name="content"
        placeholder="Write a comment..."
        required
    ></textarea>

    <button type="submit">
        Comment
    </button>

</form>

<div class="comments">
    

    <?php foreach ($comments as $comment): ?>

        <div class="comment">
            <img
                src="<?= !empty($comment['avatar'])
                ? '../' . htmlspecialchars($comment['avatar'])
                : '../assets/musicculture-default-avatar.png'; ?>"
                alt="Avatar"
                width="40"
                height="40"
            >

            <strong>
                <?= htmlspecialchars($comment['username']); ?>
            </strong>

            <p>
                <?= nl2br(htmlspecialchars($comment['content'])); ?>
            </p>

            <small>
                <?= htmlspecialchars($comment['created_at']); ?>
            </small>

        </div>

        <?php if ($_SESSION['user_id'] === $comment['user_id']): ?>

            <form action="../comments/delete_comment.php" method="POST">

                <input
                    type="hidden"
                    name="comment_id"
                    value="<?= $comment['id'] ?>"
                >

                <button type="submit">
                    Delete
                </button>

            </form>

        <?php endif; ?>

    <?php endforeach; ?>
    <p><?= $post['like_count'] ?> likes</p>
    <p><?= $post['comment_count'] ?> comments</p>

</div>

<hr>
<?php endforeach; ?>

