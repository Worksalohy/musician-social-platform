<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

header('Content-Type: application/json');

$user_id = $_SESSION["user_id"];

$post_id = $_POST["post_id"] ?? null;
$content = trim($_POST["content"] ?? "");

// 1. Validation
if (!$post_id || empty($content)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid data'
    ]);
    exit;
}

// 2. Insert comment
$sql = "INSERT INTO comments (post_id, user_id, content)
        VALUES (:post_id, :user_id, :content)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "post_id" => $post_id,
    "user_id" => $user_id,
    "content" => $content
]);

// 3. Get comment ID
$comment_id = $pdo->lastInsertId();


// 4. Fetch full comment (IMPORTANT FIX: include post_id)
$stmt = $pdo->prepare("
    SELECT comments.id,
           comments.post_id,
           comments.content,
           comments.created_at,
           users.username,
           users.avatar
    FROM comments
    JOIN users ON users.id = comments.user_id
    WHERE comments.id = ?
");

$stmt->execute([$comment_id]);

$comment = $stmt->fetch(PDO::FETCH_ASSOC);


// 5. Notifications (unchanged)
$stmt = $pdo->prepare("
    SELECT user_id
    FROM posts
    WHERE id = ?
");

$stmt->execute([$post_id]);

$post_owner = $stmt->fetchColumn();

if ($post_owner != $user_id) {
    $stmt = $pdo->prepare("
        INSERT INTO notifications
        (user_id, actor_id, post_id, type)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $post_owner,
        $user_id,
        $post_id,
        'comment'
    ]);
}


// 6. Return response
echo json_encode([
    'success' => true,
    'comment' => $comment
]);

exit;