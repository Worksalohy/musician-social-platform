<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

header("Content-Type: application/json");

$follower_id = $_SESSION['user_id'];
$following_id = $_POST['user_id'] ?? null;

if (!$following_id || $follower_id == $following_id) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request"
    ]);
    exit;
}

/* CHECK IF ALREADY FOLLOWING */
$stmt = $pdo->prepare("
    SELECT follower_id
    FROM follows
    WHERE follower_id = ? AND following_id = ?
");
$stmt->execute([$follower_id, $following_id]);
$exists = $stmt->fetch();

if ($exists) {

    /* UNFOLLOW */
    $stmt = $pdo->prepare("
        DELETE FROM follows
        WHERE follower_id = ? AND following_id = ?
    ");
    $stmt->execute([$follower_id, $following_id]);

    echo json_encode([
        "success" => true,
        "action" => "unfollow"
    ]);

} else {

    /* FOLLOW */
    $stmt = $pdo->prepare("
        INSERT INTO follows (follower_id, following_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$follower_id, $following_id]);

    echo json_encode([
        "success" => true,
        "action" => "follow"
    ]);
}