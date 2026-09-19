<?php

session_start();

require_once "../../../middleware/auth.php";


// ------------------------------------------------------------
// Check Ear Training session
// ------------------------------------------------------------

if (!isset($_SESSION['ear_training'])) {
    header("Location: index.php");
    exit;
}


// ------------------------------------------------------------
// Validate submitted answer
// ------------------------------------------------------------

if (!isset($_POST['selected_interval'])) {
    header("Location: index.php");
    exit;
}

$selectedInterval = (int) $_POST['selected_interval'];

$current =
    $_SESSION['ear_training']['current'];

$challenge =
    $_SESSION['ear_training']['challenges'][$current];


// ------------------------------------------------------------
// Check answer
// ------------------------------------------------------------

$isCorrect =
    $selectedInterval === (int) $challenge['semitones'];


// ------------------------------------------------------------
// Store first answer only
// ------------------------------------------------------------

if (!isset($_SESSION['ear_training']['answers'][$current])) {

    $_SESSION['ear_training']['answers'][$current] = [
        'selected_interval' => $selectedInterval,
        'correct_interval' => (int) $challenge['semitones'],
        'correct' => $isCorrect
    ];

    // Only the first answer can increase the score.
    if ($isCorrect) {
        $_SESSION['ear_training']['score']++;
    }
}


// ------------------------------------------------------------
// Move to next question
// ------------------------------------------------------------

$_SESSION['ear_training']['current']++;


// ------------------------------------------------------------
// Check if training is complete
// ------------------------------------------------------------

if ($_SESSION['ear_training']['current'] >= 10) {
    header("Location: result.php");
    exit;
}


// Redirect to next question with the selected level
$level = urlencode($_SESSION['ear_training']['level']);

header("Location: index.php?level={$level}");
exit;