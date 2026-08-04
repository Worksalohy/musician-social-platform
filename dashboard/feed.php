<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

$user_id = $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| LOAD POSTS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    posts.id,
    posts.user_id,
    posts.content,
    posts.created_at,

    users.username,
    users.avatar,
    users.music_level,

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
    ) AS liked_by_user,

    (
        SELECT COUNT(*)
        FROM comments
        WHERE comments.post_id = posts.id
    ) AS comment_count

FROM posts

JOIN users
ON users.id = posts.user_id

ORDER BY posts.created_at DESC
";


$stmt = $pdo->prepare($sql);

$stmt->execute([
    "user_id" => $user_id
]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);



/*
|--------------------------------------------------------------------------
| LOAD COMMENTS
|--------------------------------------------------------------------------
*/

$sql = "
SELECT

    comments.id,
    comments.post_id,
    comments.user_id,
    comments.content,
    comments.created_at,

    users.username,
    users.avatar,
    users.music_level

FROM comments

JOIN users
ON users.id = comments.user_id

ORDER BY comments.created_at ASC
";


$stmt = $pdo->prepare($sql);
$stmt->execute();

$allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);



$commentsByPost = [];

foreach ($allComments as $comment) {

    $commentsByPost[$comment['post_id']][] = $comment;

}

?>



<?php foreach ($posts as $post): ?>


<div class="post-card" id="post-<?= $post['id'] ?>">


    <!-- POST HEADER -->

    <div class="post-header">


        <div class="post-author">


            <img
                src="<?= !empty($post['avatar'])
                    ? '../' . htmlspecialchars($post['avatar'])
                    : '../assets/musicculture-default-avatar.png'
                ?>"
                alt="Avatar">


            <div class="post-info">


                <h3>

                    <a href="../profile/profile.php?id=<?= $post['user_id'] ?>">

                        <?= htmlspecialchars($post['username']) ?>

                    </a>

                </h3>


                <span class="post-level">

                    🎵 <?= htmlspecialchars($post['music_level'] ?? "Unranked") ?>

                </span>


            </div>


        </div>



        <div class="post-date">

            <?= htmlspecialchars($post['created_at']) ?>

        </div>


    </div>




    <!-- CONTENT -->


    <div class="post-content">

        <?= nl2br(htmlspecialchars($post['content'])) ?>

    </div>




    <!-- ACTIONS -->


    <div class="post-actions">


        <form class="like-form">


            <input
                type="hidden"
                name="post_id"
                value="<?= $post['id'] ?>">



            <button
                type="submit"
                class="like-button">


                <?= $post['liked_by_user']
                    ? "Unlike"
                    : "Like"
                ?>


            </button>


        </form>





        <?php if ($post['user_id'] == $_SESSION['user_id']): ?>


            <a href="../posts/edit_post.php?id=<?= $post['id'] ?>">

                Edit

            </a>



            <button
                class="delete-post-btn"
                data-post-id="<?= $post['id'] ?>">

                Delete

            </button>


        <?php endif; ?>


    </div>



    <p class="like-count">

        <?= $post['like_count'] ?> likes

    </p>





    <!-- COMMENT FORM -->


    <form
        class="comment-form"
        action="../comments/create_comment.php"
        method="POST">


        <input
            type="hidden"
            name="post_id"
            value="<?= $post['id'] ?>">



        <textarea
            name="content"
            placeholder="Write a comment..."
            required></textarea>



        <button type="submit">

            💬 Comment

        </button>


    </form>






    <!-- COMMENTS -->


    <div
        class="comments"
        id="comments-<?= $post['id'] ?>">


        <?php

        $comments = $commentsByPost[$post['id']] ?? [];

        ?>



        <?php foreach ($comments as $comment): ?>


            <div
                class="comment-wrapper"
                id="comment-<?= $comment['id'] ?>">



                <div class="comment">


                    <img
                        src="<?= !empty($comment['avatar'])
                            ? '../' . htmlspecialchars($comment['avatar'])
                            : '../assets/musicculture-default-avatar.png'
                        ?>"
                        alt="Avatar">


                    <div>


                        <strong>

                            <?= htmlspecialchars($comment['username']) ?>

                        </strong>



                        <span class="post-level">

                            🎵 <?= htmlspecialchars($comment['music_level'] ?? "Unranked") ?>

                        </span>



                        <p>

                            <?= nl2br(htmlspecialchars($comment['content'])) ?>

                        </p>



                        <small>

                            <?= htmlspecialchars($comment['created_at']) ?>

                        </small>


                    </div>


                </div>





                <?php if ($comment['user_id'] == $_SESSION['user_id']): ?>


                    <form
                        class="delete-comment-form"
                        action="../comments/delete_comment.php"
                        method="POST">


                        <input
                            type="hidden"
                            name="comment_id"
                            value="<?= $comment['id'] ?>">



                        <button type="submit">

                            Delete

                        </button>


                    </form>


                <?php endif; ?>


            </div>


        <?php endforeach; ?>


    </div>




    <p
        class="comment-total"
        data-post-id="<?= $post['id'] ?>">


        <?= $post['comment_count'] ?> comments


    </p>



</div>


<?php endforeach; ?>