<?php
session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";

if (!isset($_POST['answers'])) {
    header("Location: index.php");
    exit;
}

$answers = $_POST['answers'];
$score = 0;
$total = count($answers);

// Get correct answers
$stmt = $pdo->query("SELECT id, correct_option FROM quiz_questions");

while ($question = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $questionId = $question['id'];

    if (
        isset($answers[$questionId]) &&
        $answers[$questionId] === $question['correct_option']
    ) {
        $score++;
    }
}

// Save score in session
$_SESSION['quiz_score'] = $score;
$_SESSION['quiz_total'] = $total;

$userId = $_SESSION['user_id'];

$percentage = 0;

if ($total > 0) {
    $percentage = ($score / $total) * 100;
}

$stmt = $pdo->prepare("
    INSERT INTO quiz_results
    (user_id, score, total_questions, percentage)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $userId,
    $score,
    $total,
    $percentage
]);

// Get the user's highest percentage
$stmt = $pdo->prepare("
    SELECT MAX(percentage)
    FROM quiz_results
    WHERE user_id = ?
");

$stmt->execute([$_SESSION['user_id']]);

$bestPercentage = $stmt->fetchColumn();

if ($bestPercentage < 40) {
    $musicLevel = "Beginner";
} elseif ($bestPercentage < 70) {
    $musicLevel = "Intermediate";
} elseif ($bestPercentage < 90) {
    $musicLevel = "Advanced";
} else {
    $musicLevel = "Expert";
}

// Update the user's music level
$stmt = $pdo->prepare("
    UPDATE users
    SET music_level = ?
    WHERE id = ?
");

$stmt->execute([
    $musicLevel,
    $_SESSION['user_id']
]);

// Redirect to results page
header("Location: result.php");
exit;