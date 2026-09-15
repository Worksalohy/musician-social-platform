<?php

session_start();

require_once "../../../middleware/auth.php";
require_once "../../../config/db.php";
require_once "../config.php";
require_once "../generator.php";


// ------------------------------------------------------------
// Check Level 5 access
// ------------------------------------------------------------

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT percentage
    FROM game_results
    WHERE user_id = ?
      AND level = 4
      AND game_type = 'melody_reproduction'
    ORDER BY completed_at DESC
    LIMIT 1
");

$stmt->execute([$userId]);

$level4Score = $stmt->fetchColumn();

if ($level4Score === false || $level4Score < 80) {
    header("Location: ../index.php");
    exit;
}


// ------------------------------------------------------------
// Generate Ear Training challenge
// ------------------------------------------------------------

$challenge = generateChallenge(
    'diatonic',
    'C',
    'intermediate',
    4
);

$pageTitle = "Ear Training";

?>

<?php require_once "../../../includes/header.php"; ?>

<main class="training-game">

    <h1>Ear Training</h1>

    <p>Listen carefully and identify the interval.</p>

    <div class="ear-training">

        <button
            type="button"
            id="play-button"
            data-first-note="<?= htmlspecialchars($challenge['first_note']) ?>"
            data-second-note="<?= htmlspecialchars($challenge['second_note']) ?>"
            data-type="<?= htmlspecialchars($challenge['type']) ?>"
            data-semitones="<?= htmlspecialchars($challenge['semitones']) ?>"
        >
            ▶ Play
        </button>

    </div>

        <div class="interval-choices">

        <h2>What interval did you hear?</h2>

        <button type="button" class="interval-answer" data-interval="0">
            Unison
        </button>

        <button type="button" class="interval-answer" data-interval="1">
            Minor 2nd
        </button>

        <button type="button" class="interval-answer" data-interval="2">
            Major 2nd
        </button>

        <button type="button" class="interval-answer" data-interval="3">
            Minor 3rd
        </button>

        <button type="button" class="interval-answer" data-interval="4">
            Major 3rd
        </button>

        <button type="button" class="interval-answer" data-interval="5">
            Perfect 4th
        </button>

        <button type="button" class="interval-answer" data-interval="6">
            Tritone
        </button>

        <button type="button" class="interval-answer" data-interval="7">
            Perfect 5th
        </button>

        <button type="button" class="interval-answer" data-interval="8">
            Minor 6th
        </button>

        <button type="button" class="interval-answer" data-interval="9">
            Major 6th
        </button>

        <button type="button" class="interval-answer" data-interval="10">
            Minor 7th
        </button>

        <button type="button" class="interval-answer" data-interval="11">
            Major 7th
        </button>

        <button type="button" class="interval-answer" data-interval="12">
            Octave
        </button>

    </div>

</main>

<?php require_once "../../../includes/footer.php"; ?>

<script src="game.js"></script>