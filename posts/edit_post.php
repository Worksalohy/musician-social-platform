<?php

require_once "../middleware/auth.php";
require_once "../config/db.php";

$userId = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Post ID missing.");
}

$postId = $_GET['id'];

// Fetch post
$stmt = $pdo->prepare("
    SELECT * FROM posts WHERE id = ?
");

$stmt->execute([$postId]);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found.");
}

// Security check
if ($post['user_id'] != $userId) {
    die("Unauthorized action.");
}

// Update post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $content = trim($_POST['content']);

    if (!empty($content)) {

        $stmt = $pdo->prepare("
            UPDATE posts
            SET content = ?
            WHERE id = ?
        ");

        $stmt->execute([$content, $postId]);

        header("Location: ../dashboard/dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
</head>
<body>

    <h1>Edit Post</h1>

    <form method="POST">

        <textarea
            name="content"
            required
        ><?= htmlspecialchars($post['content']) ?></textarea>

        <button type="submit">
            Update Post
        </button>

    </form>

</body>
</html>