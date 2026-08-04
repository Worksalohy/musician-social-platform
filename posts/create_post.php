<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../dashboard/dashboard.php");
    exit;
}

$content = trim($_POST["content"] ?? "");
$user_id = $_SESSION["user_id"];

if ($content === "") {
    die("Post cannot be empty.");
}

if (strlen($content) > 1000) {
    die("Post cannot exceed 1000 characters.");
}

$sql = "
    INSERT INTO posts (user_id, content)
    VALUES (:user_id, :content)
";

try {
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        "user_id" => $user_id,
        "content" => $content
    ]);

    header("Location: ../dashboard/dashboard.php");
    exit;

} catch (PDOException $e) {
    die("Unable to create post.");
}