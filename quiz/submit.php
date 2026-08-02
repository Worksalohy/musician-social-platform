<?php
session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";

if (!isset($_POST['answers'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$answers = $_POST['answers'];

// Get the user's current quiz level
$stmt = $pdo->prepare("
    SELECT quiz_level
    FROM users
    WHERE id = ?
");
$stmt->execute([$userId]);
$level = $stmt->fetchColumn();

// Count the total number of questions for this level
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM quiz_questions
    WHERE level = ?
");
$stmt->execute([$level]);
$total = $stmt->fetchColumn();

$score = 0;

// Get the correct answers for this level
$stmt = $pdo->prepare("
    SELECT id, correct_option
    FROM quiz_questions
    WHERE level = ?
");
$stmt->execute([$level]);

while ($question = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $questionId = $question['id'];

    if (
        isset($answers[$questionId]) &&
        $answers[$questionId] === $question['correct_option']
    ) {
        $score++;
    }
}

// Calculate percentage
$percentage = 0;

if ($total > 0) {
    $percentage = round(($score / $total) * 100, 2);
}

// Save score in session
$_SESSION['quiz_score'] = $score;
$_SESSION['quiz_total'] = $total;

// Save the result
$stmt = $pdo->prepare("
    INSERT INTO quiz_results
    (user_id, level, score, total_questions, percentage)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $userId,
    $level,
    $score,
    $total,
    $percentage
]);

// Get the user's best percentage
$stmt = $pdo->prepare("
    SELECT MAX(percentage)
    FROM quiz_results
    WHERE user_id = ?
");

$stmt->execute([$userId]);

$bestPercentage = $stmt->fetchColumn();

if ($bestPercentage === null) {
    $bestPercentage = 0;
}

// Determine the user's music level
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
    $userId
]);

// Redirect to the result page
header("Location: result.php");
exit;