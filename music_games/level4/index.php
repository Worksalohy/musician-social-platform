<?php

session_start();

require_once "../../middleware/auth.php";
require_once "../../config/db.php";


// ------------------------------------------------------------
// Start a new Level 4 game if none is active
// ------------------------------------------------------------

if (
    !isset($_SESSION['level4_games']) ||
    !isset($_SESSION['level4_current']) ||
    !isset($_SESSION['level4_score'])
) {

    $stmt = $pdo->prepare("
        SELECT
            id,
            audio_file
        FROM music_games
        WHERE level = 4
          AND game_type = 'melody_reproduction'
        ORDER BY RAND()
        LIMIT 10
    ");

    $stmt->execute();

    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (count($games) < 10) {
        die("Not enough Level 4 melodies available.");
    }


    $_SESSION['level4_games'] = $games;
    $_SESSION['level4_current'] = 0;
    $_SESSION['level4_score'] = 0;

} else {

    // Continue the existing game
    $games = $_SESSION['level4_games'];
}


// ------------------------------------------------------------
// Get current melody
// ------------------------------------------------------------

$currentIndex = (int) $_SESSION['level4_current'];


// Make sure the current melody exists

if (!isset($games[$currentIndex])) {
    header("Location: index.php");
    exit;
}


$currentGame = $games[$currentIndex];


// ------------------------------------------------------------
// Page settings
// ------------------------------------------------------------

$pageTitle = "Level 4 | Music Training";

$pageStyles = [
    "/music_games/level4/game.css"
];

$pageScripts = [
    "/music_games/level4/game.js"
];

require_once "../../includes/header.php";

?>

<div class="level4-game">

    <script>
        const gameAudio =
            <?= json_encode($currentGame['audio_file']) ?>;
    </script>


    <h1>🎹 Level 4 — Reproduce the Melody</h1>

    <p class="instruction">
        Listen to the melody, then reproduce it using the piano.
    </p>


    <div class="melody-progress">

        <strong>
            Melody <?= $currentIndex + 1; ?> / 10
        </strong>

    </div>


    <div class="melody-controls">

        <button id="play-melody">
            ▶ Play Melody
        </button>

        <button id="clear-sequence">
            ↺ Clear
        </button>

    </div>


    <div class="sequence-display">

        <p>
            Your melody:
        </p>

        <div id="user-sequence">
            —
        </div>

    </div>


    <div class="piano">

        <button class="key white-key" data-note="1">
            <span>C</span>
            <small>1</small>
        </button>

        <button class="key white-key" data-note="2">
            <span>D</span>
            <small>2</small>
        </button>

        <button class="key white-key" data-note="3">
            <span>E</span>
            <small>3</small>
        </button>

        <button class="key white-key" data-note="4">
            <span>F</span>
            <small>4</small>
        </button>

        <button class="key white-key" data-note="5">
            <span>G</span>
            <small>5</small>
        </button>

        <button class="key white-key" data-note="6">
            <span>A</span>
            <small>6</small>
        </button>

        <button class="key white-key" data-note="7">
            <span>B</span>
            <small>7</small>
        </button>

    </div>


    <div class="submit-area">

        <button id="submit-melody">
            ✓ Submit Melody
        </button>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>