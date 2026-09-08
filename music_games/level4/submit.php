<?php

session_start();

require_once "../../middleware/auth.php";
require_once "../../config/db.php";


// ------------------------------------------------------------
// Get current user
// ------------------------------------------------------------

$userId = $_SESSION['user_id'];


// ------------------------------------------------------------
// Get submitted data
// ------------------------------------------------------------

$userSequence = $_POST['user_sequence'] ?? [];
$targetSequence = $_POST['target_sequence'] ?? [];


// Make sure both are arrays

if (!is_array($userSequence) || !is_array($targetSequence)) {
    header("Location: index.php");
    exit;
}


// ------------------------------------------------------------
// Calculate score
// ------------------------------------------------------------

$totalNotes = count($targetSequence);

$correctNotes = 0;

for ($i = 0; $i < min(count($userSequence), $totalNotes); $i++) {

    if ((string) $userSequence[$i] === (string) $targetSequence[$i]) {
        $correctNotes++;
    }
}


// ------------------------------------------------------------
// Calculate percentage
// ------------------------------------------------------------

$percentage = $totalNotes > 0
    ? round(($correctNotes / $totalNotes) * 100, 2)
    : 0;


// ------------------------------------------------------------
// Save result
// ------------------------------------------------------------

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
    $correctNotes,
    $totalNotes,
    $percentage
]);


// ------------------------------------------------------------
// Go to result page
// ------------------------------------------------------------

header("Location: result.php");
exit;