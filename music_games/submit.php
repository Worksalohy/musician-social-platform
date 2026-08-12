<?php

session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";


// Make sure the game was actually started
if (
    !isset($_SESSION['game_questions']) ||
    !isset($_SESSION['played_game_level'])
) {
    header("Location: index.php");
    exit;
}


$userId = (int) $_SESSION['user_id'];

$answers = $_POST['answers'] ?? [];


// Questions that were actually displayed
$questionIds = $_SESSION['game_questions'];


// If the user submits without answers
if (empty($answers)) {
    header("Location: index.php");
    exit;
}


// Get the game level
$level = (int) $_SESSION['played_game_level'];


// Get the game type
$gameType = $_SESSION['played_game_type'] ?? null;


// Build placeholders
$placeholders = implode(
    ',',
    array_fill(0, count($questionIds), '?')
);


// Get correct answers for the questions that were played
$stmt = $pdo->prepare("
    SELECT id, correct_option, game_type
    FROM music_games
    WHERE id IN ($placeholders)
");

$stmt->execute($questionIds);


$score = 0;
$total = 0;


while ($game = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $total++;

    $gameId = $game['id'];

    // Keep the game type from the database
    $gameType = $game['game_type'];

    // Check user's answer
    if (
        isset($answers[$gameId]) &&
        $answers[$gameId] === $game['correct_option']
    ) {
        $score++;
    }
}


// Calculate percentage
$percentage = 0;

if ($total > 0) {
    $percentage = round(
        ($score / $total) * 100,
        2
    );
}


// Save game result
$stmt = $pdo->prepare("
    INSERT INTO game_results
    (
        user_id,
        level,
        game_type,
        score,
        total_questions,
        percentage
    )
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $userId,
    $level,
    $gameType,
    $score,
    $total,
    $percentage
]);


// Unlock Level 4 after passing Level 3
if ($level === 3 && $percentage >= 70) {

    $stmt = $pdo->prepare("
        UPDATE users
        SET skill_level = 4
        WHERE id = ?
    ");

    $stmt->execute([$userId]);
}


// Store result temporarily in session
$_SESSION['game_score'] = $score;
$_SESSION['game_total'] = $total;
$_SESSION['game_percentage'] = $percentage;


// Remove the questions from the current game
unset($_SESSION['game_questions']);
unset($_SESSION['played_game_level']);
unset($_SESSION['played_game_type']);


// Go to result page
header("Location: result.php");
exit;