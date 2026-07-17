<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";

header("Content-Type: application/json");

$user_id = $_SESSION['user_id'];

$q = trim($_GET['q'] ?? '');

if ($q === '') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, username, instrument, avatar
    FROM users
    WHERE id != ?
    AND (
        username LIKE ?
        OR instrument LIKE ?
    )
    ORDER BY username ASC
    LIMIT 5
");

$keyword = "%{$q}%";

$stmt->execute([
    $user_id,
    $keyword,
    $keyword
]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);