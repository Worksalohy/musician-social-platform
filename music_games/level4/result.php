<?php

session_start();

require_once "../../middleware/auth.php";
require_once "../../config/db.php";


// ------------------------------------------------------------
// Get current user
// ------------------------------------------------------------

$userId = $_SESSION['user_id'];


// ------------------------------------------------------------
// Get latest Level 4 result
// ------------------------------------------------------------

$stmt = $pdo->prepare("
    SELECT
        score,
        total_questions,
        percentage,
        completed_at
    FROM game_results
    WHERE user_id = ?
      AND level = 4
      AND game_type = 'melody_reproduction'
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$userId]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$result) {
    header("Location: index.php");
    exit;
}


// ------------------------------------------------------------
// Page settings
// ------------------------------------------------------------

$pageTitle = "Level 4 Result | Music Training";

require_once "../../includes/header.php";

?>


<div class="game-result">

    <h1>🎧 Level 4 Complete!</h1>

    <h2>🎹 Melody Reproduction</h2>

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


    <div class="result-actions">

        <a href="index.php">
            ▶ Play Again
        </a>

        <a href="../index.php">
            ← Back to Music Games
        </a>

    </div>

</div>


<?php

require_once "../../includes/footer.php";

?>