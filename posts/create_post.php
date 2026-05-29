<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];

    // Prevent empty posts
    if (empty($content)) {
        die("Post cannot be empty.");
    }

    $sql = "INSERT INTO posts (user_id, content)
            VALUES (:user_id, :content)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "user_id" => $user_id,
        "content" => $content
    ]);

    header("Location: ../dashboard/dashboard.php");
    exit;
}
?>