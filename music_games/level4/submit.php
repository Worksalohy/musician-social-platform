<?php

session_start();

require_once "../../middleware/auth.php";
require_once "../../config/db.php";


// ------------------------------------------------------------
// Make sure Level 4 was started
// ------------------------------------------------------------

if (
    !isset($_SESSION['level4_games']) ||
    !isset($_SESSION['level4_current']) ||
    !isset($_SESSION['level4_score'])
) {
    header("Location: index.php");
    exit;
}


// ------------------------------------------------------------
// Get current user
// ------------------------------------------------------------

$userId = (int) $_SESSION['user_id'];


// ------------------------------------------------------------
// Get current melody
// ------------------------------------------------------------

$currentIndex = (int) $_SESSION['level4_current'];
$games = $_SESSION['level4_games'];


// Make sure the current index is valid

if (!isset($games[$currentIndex])) {
    header("Location: index.php");
    exit;
}


$currentGameId = (int) $games[$currentIndex]['id'];


// ------------------------------------------------------------
// Get user's sequence
// ------------------------------------------------------------

$userSequence = $_POST['user_sequence'] ?? [];


// Make sure the submitted sequence is an array

if (!is_array($userSequence)) {
    header("Location: index.php");
    exit;
}


// ------------------------------------------------------------
// Get the correct sequence from the database
// ------------------------------------------------------------

$stmt = $pdo->prepare("
    SELECT target_sequence
    FROM music_games
    WHERE id = ?
      AND level = 4
      AND game_type = 'melody_reproduction'
    LIMIT 1
");

$stmt->execute([$currentGameId]);

$targetSequence = $stmt->fetchColumn();


if ($targetSequence === false) {
    die("Melody not found.");
}


// Convert target sequence into an array

$targetSequence = preg_split(
    '/\s+/',
    trim($targetSequence)
);


// ------------------------------------------------------------
// Check the complete melody
// ------------------------------------------------------------

$melodyCorrect = (
    count($userSequence) === count($targetSequence)
    &&
    $userSequence === $targetSequence
);


// ------------------------------------------------------------
// Add one point only if the entire melody is correct
// ------------------------------------------------------------

if ($melodyCorrect) {
    $_SESSION['level4_score']++;
}


// ------------------------------------------------------------
// Move to the next melody
// ------------------------------------------------------------

$_SESSION['level4_current']++;


// ------------------------------------------------------------
// Check whether all 10 melodies are finished
// ------------------------------------------------------------

if ($_SESSION['level4_current'] >= count($games)) {

    $score = (int) $_SESSION['level4_score'];

    $total = count($games);

    $percentage = $total > 0
        ? round(($score / $total) * 100, 2)
        : 0;


    // --------------------------------------------------------
    // Save final Level 4 result
    // --------------------------------------------------------

    $stmt = $pdo->prepare("
        INSERT INTO game_results (
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
        4,
        'melody_reproduction',
        $score,
        $total,
        $percentage
    ]);


    // --------------------------------------------------------
    // Complete Level 4
    // --------------------------------------------------------

    if ($percentage >= 70) {

        $stmt = $pdo->prepare("
            UPDATE users
            SET skill_level = 4,
                music_level = 'Expert'
            WHERE id = ?
        ");

        $stmt->execute([$userId]);
    }


    // --------------------------------------------------------
    // Clean Level 4 session data
    // --------------------------------------------------------

    unset($_SESSION['level4_games']);
    unset($_SESSION['level4_current']);
    unset($_SESSION['level4_score']);


    // --------------------------------------------------------
    // Go to final result
    // --------------------------------------------------------

    header("Location: result.php");
    exit;
}


// ------------------------------------------------------------
// More melodies remaining
// ------------------------------------------------------------

header("Location: index.php");
exit;