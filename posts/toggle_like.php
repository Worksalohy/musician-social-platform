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


    // Check post exists
    $stmt = $pdo->prepare("
        SELECT user_id
        FROM posts
        WHERE id = :post_id
    ");

    $stmt->execute([
        "post_id" => $post_id
    ]);

    $post_owner = $stmt->fetchColumn();


    if (!$post_owner) {

        echo json_encode([
            "success" => false,
            "message" => "Post not found"
        ]);

        exit;
    }



    // Check existing like
    $stmt = $pdo->prepare("
        SELECT id
        FROM likes
        WHERE user_id = :user_id
        AND post_id = :post_id
    ");


    $stmt->execute([
        "user_id" => $user_id,
        "post_id" => $post_id
    ]);


    $existingLike = $stmt->fetch();



    if ($existingLike) {


        // Remove like
        $stmt = $pdo->prepare("
            DELETE FROM likes
            WHERE user_id = :user_id
            AND post_id = :post_id
        ");


        $stmt->execute([
            "user_id" => $user_id,
            "post_id" => $post_id
        ]);


        $liked = false;



    } else {


        // Add like
        $stmt = $pdo->prepare("
            INSERT INTO likes (user_id, post_id)
            VALUES (:user_id, :post_id)
        ");


        $stmt->execute([
            "user_id" => $user_id,
            "post_id" => $post_id
        ]);


        $liked = true;



        // Create notification
        if ($post_owner != $user_id) {


            $stmt = $pdo->prepare("
                INSERT INTO notifications
                (user_id, actor_id, post_id, type)
                VALUES (:user_id, :actor_id, :post_id, 'like')
            ");


            $stmt->execute([
                "user_id" => $post_owner,
                "actor_id" => $user_id,
                "post_id" => $post_id
            ]);

        }

    }



    // Updated like count
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM likes
        WHERE post_id = :post_id
    ");


    $stmt->execute([
        "post_id" => $post_id
    ]);


    $count = $stmt->fetchColumn();



    echo json_encode([
        "success" => true,
        "liked" => $liked,
        "count" => $count
    ]);



} catch (PDOException $e) {


    echo json_encode([
        "success" => false,
        "message" => "Unable to process like"
    ]);

}


exit;