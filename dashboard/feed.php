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


/* ================================
   NEW: LOAD ALL COMMENTS ONCE
================================ */
$sql = "SELECT comments.id,
               comments.post_id,
               comments.user_id,
               comments.content,
               comments.created_at,
               users.username,
               users.avatar
        FROM comments
        JOIN users ON users.id = comments.user_id
        ORDER BY comments.created_at ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* GROUP COMMENTS BY POST ID */
$commentsByPost = [];

foreach ($allComments as $comment) {
    $commentsByPost[$comment['post_id']][] = $comment;
}
?>


<?php foreach ($posts as $post): ?>

    <div class="post">

        <?php if (!empty($post["avatar"])): ?>
            <img src="../<?= htmlspecialchars($post["avatar"]) ?>"
                 alt="Avatar"
                 width="50">
        <?php else: ?>
            <img src="../assets/musicculture-default-avatar.png"
                 alt="Default Avatar"
                 width="50">
        <?php endif; ?>

        <h3><?= htmlspecialchars($post["username"]) ?></h3>

        <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>

        <small><?= htmlspecialchars($post["created_at"]) ?></small>
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

    <form class="like-form">

        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

        <button type="submit" class="like-button">
            <?php if ($post['liked_by_user']): ?>
                Unlike
            <?php else: ?>
                Like
            <?php endif; ?>
        </button>

    </form>

    <!-- COMMENT FORM -->
    <form class="comment-form" action="../comments/create_comment.php" method="POST">

        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

        <textarea name="content" placeholder="Write a comment..." required></textarea>

        <button type="submit">Comment</button>

    </form>

    <div class="comments" id="comments-<?= $post['id'] ?>">

        <!-- GET COMMENTS FROM GROUPED ARRAY -->
        <?php $comments = $commentsByPost[$post['id']] ?? []; ?>

        <?php foreach ($comments as $comment): ?>

            <div class="comment-wrapper" id="comment-<?= $comment['id'] ?>">

                <div class="comment">

                    <img
                        src="<?= !empty($comment['avatar'])
                            ? '../' . htmlspecialchars($comment['avatar'])
                            : '../assets/musicculture-default-avatar.png'; ?>"
                        alt="Avatar"
                        width="40"
                        height="40"
                    >

                    <strong><?= htmlspecialchars($comment['username']); ?></strong>

                    <p><?= nl2br(htmlspecialchars($comment['content'])); ?></p>

                    <small><?= htmlspecialchars($comment['created_at']); ?></small>

                </div>

                <?php if ($_SESSION['user_id'] === $comment['user_id']): ?>

                    <form action="../comments/delete_comment.php"
                          method="POST"
                          class="delete-comment-form">

                        <input type="hidden" name="comment_id"
                               value="<?= $comment['id'] ?>">

                        <button type="submit">Delete</button>

                    </form>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

        <p class="like-count">
            <?= $post['like_count'] ?> likes
        </p>

        <p class="comment-count">
            <?= $post['comment_count'] ?> comments
        </p>

    </div>

    <hr>

<?php endforeach; ?>


<!-- YOUR JS STAYS EXACTLY THE SAME -->
<script>
document.querySelectorAll('.comment-form').forEach(form => {

    form.addEventListener('submit', function(e) {

        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {

            if (data.success) {

                const c = data.comment;

                const commentsContainer =
                    document.getElementById(`comments-${c.post_id}`);

                const newComment = document.createElement('div');
                newComment.classList.add('comment-wrapper');
                newComment.id = `comment-${c.id}`;

                newComment.innerHTML = `
                    <div class="comment">

                        <img src="${c.avatar ? '../' + c.avatar : '../assets/musicculture-default-avatar.png'}" width="40">

                        <strong>${c.username}</strong>

                        <p>${c.content}</p>

                        <small>${c.created_at}</small>

                    </div>

                    <form action="../comments/delete_comment.php"
                          method="POST"
                          class="delete-comment-form">

                        <input type="hidden" name="comment_id" value="${c.id}">
                        <button type="submit">Delete</button>

                    </form>
                `;

                commentsContainer.prepend(newComment);
                this.querySelector('textarea').value = '';
            }
        });
    });
});
</script>


<script>
document.addEventListener('submit', function(e) {

    if (!e.target.classList.contains('delete-comment-form')) return;

    e.preventDefault();

    if (!confirm('Delete this comment?')) return;

    const formData = new FormData(e.target);

    fetch(e.target.action, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {

        if (data.success) {

            const id =
                e.target.querySelector('input[name="comment_id"]').value;

            document.getElementById(`comment-${id}`).remove();
        }
    });
});
</script>