<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$comment_id = $_POST["comment_id"] ?? null;
$user_id = $_SESSION["user_id"];

if (!$comment_id) {
    header("Location: ../dashboard/dashboard.php");
    exit;
}

$sql = "DELETE FROM comments
        WHERE id = :id
        AND user_id = :user_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "id" => $comment_id,
    "user_id" => $user_id
]);

header("Location: ../dashboard/dashboard.php");
exit;