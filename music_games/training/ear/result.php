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
// Get result
// ------------------------------------------------------------

$score = $_SESSION['ear_training']['score'];

$totalQuestions =
    count($_SESSION['ear_training']['challenges']);

$percentage =
    ($score / $totalQuestions) * 100;


// ------------------------------------------------------------
// Interval names
// ------------------------------------------------------------

$intervalNames = [
    0 => "Unison",
    1 => "Minor 2nd",
    2 => "Major 2nd",
    3 => "Minor 3rd",
    4 => "Major 3rd",
    5 => "Perfect 4th",
    6 => "Tritone",
    7 => "Perfect 5th",
    8 => "Minor 6th",
    9 => "Major 6th",
    10 => "Minor 7th",
    11 => "Major 7th",
    12 => "Octave"
];


// ------------------------------------------------------------
// Calculate interval accuracy
// ------------------------------------------------------------

$intervalStats = [];

foreach ($_SESSION['ear_training']['answers'] as $answer) {

    $correctInterval =
        (int) $answer['correct_interval'];

    if (!isset($intervalStats[$correctInterval])) {

        $intervalStats[$correctInterval] = [
            'total' => 0,
            'correct' => 0
        ];
    }

    $intervalStats[$correctInterval]['total']++;

    if ($answer['correct']) {
        $intervalStats[$correctInterval]['correct']++;
    }
}


// ------------------------------------------------------------
// Calculate percentages
// ------------------------------------------------------------

foreach ($intervalStats as $interval => &$stats) {

    $stats['percentage'] =
        ($stats['correct'] / $stats['total']) * 100;
}

unset($stats);


$pageTitle = "Ear Training Result";

$pageStyles = [
    "/music_games/training/ear/game.css"
];

?>

<?php require_once "../../../includes/header.php"; ?>

<main class="training-game">

    <h1>Ear Training Complete!</h1>

    <p>
        Score: <?= $score ?> / <?= $totalQuestions ?>
    </p>


    <h2>Interval Accuracy</h2>

    <?php if (!empty($intervalStats)): ?>

        <div class="interval-results">

            <?php foreach ($intervalStats as $interval => $stats): ?>

                <?php
                $accuracy = $stats['percentage'];

                $accuracyClass =
                    $accuracy == 100
                        ? 'accuracy-good'
                        : 'accuracy-needs-practice';
                ?>

                <div class="interval-result">

                    <span class="interval-name">
                        <?= htmlspecialchars(
                            $intervalNames[$interval]
                        ) ?>
                    </span>

                    <span
                        class="<?= $accuracyClass ?>"
                    >
                        <?= rtrim(
                            rtrim(
                                number_format(
                                    $accuracy,
                                    1
                                ),
                                '0'
                            ),
                            '.'
                        ) ?>%
                    </span>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <p>
        Accuracy: <?= $percentage ?>%
    </p>

</main>

<?php require_once "../../../includes/footer.php"; ?>