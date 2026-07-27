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

// Redirect to results page
header("Location: result.php");
exit;