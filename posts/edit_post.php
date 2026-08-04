<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    die("Post ID missing.");
}

$post_id = (int) $_GET["id"];

try {

    // Fetch post
    $stmt = $pdo->prepare("
        SELECT id, content, user_id
        FROM posts
        WHERE id = :id
    ");

    $stmt->execute([
        "id" => $post_id
    ]);

    $post = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$post) {
        die("Post not found.");
    }


    // Check ownership
    if ($post["user_id"] != $user_id) {
        die("Unauthorized action.");
    }


    // Update post
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $content = trim($_POST["content"] ?? "");


        if ($content === "") {
            die("Post cannot be empty.");
        }


        if (strlen($content) > 1000) {
            die("Post cannot exceed 1000 characters.");
        }


        $update = $pdo->prepare("
            UPDATE posts
            SET content = :content
            WHERE id = :post_id
            AND user_id = :user_id
        ");


        $update->execute([
            "content" => $content,
            "post_id" => $post_id,
            "user_id" => $user_id
        ]);


        header("Location: ../dashboard/dashboard.php");
        exit;
    }


} catch (PDOException $e) {

    die("Unable to edit post.");

}

?>


<?php require_once "../includes/header.php"; ?>


<div class="container">

    <h1>Edit Post</h1>


    <form method="POST">

        <textarea
            name="content"
            rows="5"
            required
        ><?= htmlspecialchars($post["content"]) ?></textarea>


        <button type="submit">
            Update Post
        </button>

    </form>

</div>


<?php require_once "../includes/footer.php"; ?>