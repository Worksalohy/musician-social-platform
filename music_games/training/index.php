<?php

session_start();

require_once "../../middleware/auth.php";
require_once "../../config/db.php";


// ------------------------------------------------------------
// Get current user
// ------------------------------------------------------------

$userId = $_SESSION['user_id'];


// ------------------------------------------------------------
// Check Level 4 result
// ------------------------------------------------------------

$stmt = $pdo->prepare("
    SELECT percentage
    FROM game_results
    WHERE user_id = ?
      AND level = 4
      AND game_type = 'melody_reproduction'
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$userId]);

$level4Score = $stmt->fetchColumn();


// ------------------------------------------------------------
// Music Training access
// ------------------------------------------------------------

if ($level4Score === false || $level4Score < 80) {
    header("Location: ../index.php");
    exit;
}


// ------------------------------------------------------------
// Page setup
// ------------------------------------------------------------

$pageStyles = [
    "/music_games/assets/css/music_games.css"
];

require_once "../../includes/header.php";

?>


<div class="game-container">

    <h1>🎵 Music Training</h1>

    <p>
        Practice and develop your musical skills.
    </p>


    <h2>Training Activities</h2>


    <div class="training-progress">

        <a href="ear/index.php" class="training-level current">

            <p>
                🎧 Ear Training
            </p>

            <small>
                Improve your ability to recognize musical sounds.
            </small>

        </a>


        <div class="training-level locked">

            <p>
                🎹 Melody Training
            </p>

            <small>
                Practice hearing and reproducing melodies.
            </small>

        </div>


        <div class="training-level locked">

            <p>
                🎼 Chord Training
            </p>

            <small>
                Develop your chord recognition skills.
            </small>

        </div>


        <div class="training-level locked">

            <p>
                🥁 Rhythm Training
            </p>

            <small>
                Develop your sense of rhythm and timing.
            </small>

        </div>

    </div>


    <p>
        More training activities will be available soon.
    </p>


    <a href="../index.php">
        Back to Music Games
    </a>

</div>


<?php

require_once "../../includes/footer.php";

?>