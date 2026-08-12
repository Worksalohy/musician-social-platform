<?php

session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";


$userId = $_SESSION['user_id'];


// Get the latest game result
$stmt = $pdo->prepare("
    SELECT
        level,
        game_type,
        score,
        total_questions,
        percentage,
        completed_at
    FROM game_results
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$userId]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$result) {
    header("Location: index.php");
    exit;
}


// Determine training title

if ((int) $result['level'] === 3) {

    $trainingTitle = "Ear Training";

} else {

    $trainingTitle =
        ucfirst($result['game_type']) . " Recognition";
}

?>


<div class="game-result">

    <h1>🎧 Music Training Complete!</h1>

    <h2>
        <?= htmlspecialchars($trainingTitle); ?>
    </h2>

    <p>
        Level:
        <strong>
            <?= htmlspecialchars($result['level']); ?>
        </strong>
    </p>

    <p>
        Score:
        <strong>
            <?= htmlspecialchars($result['score']); ?>
            /
            <?= htmlspecialchars($result['total_questions']); ?>
        </strong>
    </p>

    <p>
        Accuracy:
        <strong>
            <?= htmlspecialchars($result['percentage']); ?>%
        </strong>
    </p>

    <a href="play.php">
        Play Again
    </a>

    <a href="index.php">
        Back to Music Games
    </a>

</div>


<?php

require_once "../includes/footer.php";

?>