<?php

session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";


if (!isset($_POST['answers'])) {
    header("Location: index.php");
    exit;
}


$userId = (int) $_SESSION['user_id'];
$answers = $_POST['answers'];


// Get the user's current quiz level
$stmt = $pdo->prepare("
    SELECT skill_level
    FROM users
    WHERE id = ?
");

$stmt->execute([$userId]);

$level = (int) $stmt->fetchColumn();


// Count the total number of questions for this level
$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM quiz_questions
    WHERE level = ?
");

$stmt->execute([$level]);

$total = (int) $stmt->fetchColumn();


$score = 0;


// Get correct answers for this level
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
    $percentage = round(
        ($score / $total) * 100,
        2
    );
}


// Save score in session
$_SESSION['quiz_score'] = $score;
$_SESSION['quiz_total'] = $total;
$_SESSION['quiz_percentage'] = $percentage;


// Save quiz result
$stmt = $pdo->prepare("
    INSERT INTO quiz_results
    (
        user_id,
        level,
        score,
        total_questions,
        percentage
    )
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $userId,
    $level,
    $score,
    $total,
    $percentage
]);


// --------------------------------------------------
// LEVEL PROGRESSION
// --------------------------------------------------

$passed = $percentage >= 70;


// If the user passed the current level,
// unlock the next level.
if ($passed) {

    if ($level === 1) {

        // Passed Quiz Level 1
        $newSkillLevel = 2;
        $newMusicLevel = "Intermediate";

    } elseif ($level === 2) {

        // Passed Quiz Level 2
        $newSkillLevel = 3;
        $newMusicLevel = "Advanced";

    } else {

        // Safety fallback
        $newSkillLevel = $level;
        $newMusicLevel = null;
    }


    // Update both progression and displayed music level
    if ($newMusicLevel !== null) {

        $stmt = $pdo->prepare("
            UPDATE users
            SET skill_level = ?,
                music_level = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $newSkillLevel,
            $newMusicLevel,
            $userId
        ]);
    }
}


// Redirect to result page
header("Location: result.php");
exit;