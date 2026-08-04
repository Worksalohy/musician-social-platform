<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

header("Content-Type: application/json");

$user_id = $_SESSION["user_id"];


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);

    exit;
}


$post_id = isset($_POST["post_id"]) 
    ? (int) $_POST["post_id"] 
    : null;


if (!$post_id) {

    echo json_encode([
        "success" => false,
        "message" => "Post ID missing"
    ]);

    exit;
}


try {

    // Check if post exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT id
        FROM posts
        WHERE id = :post_id
        AND user_id = :user_id
    ");

    $stmt->execute([
        "post_id" => $post_id,
        "user_id" => $user_id
    ]);


    if ($stmt->rowCount() === 0) {

        echo json_encode([
            "success" => false,
            "message" => "Unauthorized or post not found"
        ]);

        exit;
    }


    // Delete post
    $delete = $pdo->prepare("
        DELETE FROM posts
        WHERE id = :post_id
        AND user_id = :user_id
    ");


    $delete->execute([
        "post_id" => $post_id,
        "user_id" => $user_id
    ]);


    echo json_encode([
        "success" => true,
        "message" => "Post deleted successfully"
    ]);


} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to delete post"
    ]);

}