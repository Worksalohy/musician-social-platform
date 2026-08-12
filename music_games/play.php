<?php

session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";


// Get user's skill level
$stmt = $pdo->prepare("
    SELECT skill_level
    FROM users
    WHERE id = ?
");

$stmt->execute([$_SESSION['user_id']]);

$level = $stmt->fetchColumn();

$_SESSION['game_level'] = $level;


// Block users below Level 3
if ($level < 3) {
    header("Location: index.php");
    exit;
}


// --------------------------------------------------
// Fetch Level 3 games
// 3 chord + 3 melody + 2 note degree + 2 interval
// --------------------------------------------------

if ($level == 3) {

    // Chord sequences
    $stmt = $pdo->prepare("
        SELECT *
        FROM music_games
        WHERE level = ?
        AND game_type = 'chord_sequence'
        ORDER BY RAND()
        LIMIT 3
    ");

    $stmt->execute([$level]);
    $chordGames = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Melodies
    $stmt = $pdo->prepare("
        SELECT *
        FROM music_games
        WHERE level = ?
        AND game_type = 'melody'
        ORDER BY RAND()
        LIMIT 3
    ");

    $stmt->execute([$level]);
    $melodyGames = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Note degrees
    $stmt = $pdo->prepare("
        SELECT *
        FROM music_games
        WHERE level = ?
        AND game_type = 'note_degree'
        ORDER BY RAND()
        LIMIT 2
    ");

    $stmt->execute([$level]);
    $noteDegreeGames = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Intervals
    $stmt = $pdo->prepare("
        SELECT *
        FROM music_games
        WHERE level = ?
        AND game_type = 'interval'
        ORDER BY RAND()
        LIMIT 2
    ");

    $stmt->execute([$level]);
    $intervalGames = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Combine everything
    $games = array_merge(
        $chordGames,
        $melodyGames,
        $noteDegreeGames,
        $intervalGames
    );


    // Randomize the order
    shuffle($games);

} else {

    // --------------------------------------------------
    // Other levels
    // --------------------------------------------------

    $stmt = $pdo->prepare("
        SELECT *
        FROM music_games
        WHERE level = ?
        ORDER BY RAND()
        LIMIT 10
    ");

    $stmt->execute([$level]);

    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Remember the questions
$_SESSION['game_questions'] = array_column($games, 'id');

if (!empty($games)) {
    $_SESSION['played_game_level'] = $games[0]['level'];
}

$pageScripts = [
    "/assets/js/music_games.js"
];

// Load header only after PHP redirects are finished
require_once "../includes/header.php";

?>


<div class="game-container">

    <h1>🎧 Music Training Game</h1>


    <form action="submit.php" method="POST">


    <?php if (count($games) > 0): ?>


        <?php foreach ($games as $index => $game): ?>


        <div class="game-question">


            <h3>
                Question <?= $index + 1; ?>
            </h3>


            <p>
                <?= htmlspecialchars($game['question']); ?>
            </p>


            <audio class="game-audio" controls>
                <source 
                    src="assets/audio/<?= htmlspecialchars($game['audio_file']); ?>"
                    type="audio/mpeg"
                >
                Your browser does not support audio.
            </audio>


            <div>

                <label>
                    <input 
                    type="radio"
                    name="answers[<?= $game['id']; ?>]"
                    value="A"
                    required>

                    <?= htmlspecialchars($game['option_a']); ?>

                </label>

            </div>


            <div>

                <label>
                    <input 
                    type="radio"
                    name="answers[<?= $game['id']; ?>]"
                    value="B">

                    <?= htmlspecialchars($game['option_b']); ?>

                </label>

            </div>


            <div>

                <label>
                    <input 
                    type="radio"
                    name="answers[<?= $game['id']; ?>]"
                    value="C">

                    <?= htmlspecialchars($game['option_c']); ?>

                </label>

            </div>


            <div>

                <label>
                    <input 
                    type="radio"
                    name="answers[<?= $game['id']; ?>]"
                    value="D">

                    <?= htmlspecialchars($game['option_d']); ?>

                </label>

            </div>


        </div>


        <?php endforeach; ?>


        <button type="submit">
            Submit Game
        </button>


    <?php else: ?>


        <p>
            No games available for this level.
        </p>


    <?php endif; ?>


    </form>


</div>

<?php


require_once "../includes/footer.php";

?>