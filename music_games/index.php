<?php

session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";

$pageStyles = [
    "/music_games/assets/css/music_games.css"
];

require_once "../includes/header.php";


$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT skill_level, music_level
    FROM users
    WHERE id = ?
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get completed quiz levels
$stmt = $pdo->prepare("
    SELECT level
    FROM quiz_results
    WHERE user_id = ?
    GROUP BY level
");

$stmt->execute([$userId]);

$completedQuizLevels = $stmt->fetchAll(PDO::FETCH_COLUMN);


// Get completed quiz levels and scores
$stmt = $pdo->prepare("
    SELECT level, score, total_questions, percentage
    FROM quiz_results
    WHERE user_id = ?
    ORDER BY level
");

$stmt->execute([$userId]);

$quizResults = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Organize quiz results by level
$quizByLevel = [];

foreach ($quizResults as $result) {
    $quizByLevel[$result['level']] = $result;
}


?>


<div class="game-container">

    <h1>🎧 Music Training Game</h1>

    <p>
        Your current level:
        <strong>
            <?= htmlspecialchars($user['music_level']); ?>
        </strong>
    </p>

    <h2>Your Progress</h2>

    <div class="training-progress">

        
        <!-- Level 1 -->
<div class="training-level completed">

    <?php if (in_array(1, $completedQuizLevels)): ?>

        <p>
            ✓ Level 1 — Music Quiz
        </p>

        <small>
            Score:
            <?= htmlspecialchars($quizByLevel[1]['score']); ?> /
            <?= htmlspecialchars($quizByLevel[1]['total_questions']); ?>
            —
            <?= htmlspecialchars($quizByLevel[1]['percentage']); ?>%
        </small>

    <?php else: ?>

        <p>
            🔒 Level 1 — Music Quiz
        </p>

    <?php endif; ?>

</div>

        
        <!-- Level 2 -->
<div class="training-level completed">

    <?php if (in_array(2, $completedQuizLevels)): ?>

        <p>
            ✓ Level 2 — Music Quiz
        </p>

        <small>
            Score:
            <?= htmlspecialchars($quizByLevel[2]['score']); ?> /
            <?= htmlspecialchars($quizByLevel[2]['total_questions']); ?>
            —
            <?= htmlspecialchars($quizByLevel[2]['percentage']); ?>%
        </small>

    <?php else: ?>

        <p>
            🔒 Level 2 — Music Quiz
        </p>

    <?php endif; ?>

</div>

        
        <!-- Level 3 -->
<div class="training-level <?= $user['skill_level'] == 3 ? 'current' : ($user['skill_level'] > 3 ? 'completed' : 'locked'); ?>">

    <?php if ($user['skill_level'] >= 3): ?>

        <p>
            ✓ Level 3 — Ear Training
        </p>

        <small>
            Test your musical ear
        </small>

    <?php else: ?>

        <p>
            🔒 Level 3 — Ear Training
        </p>

        <small>
            Complete Levels 1 and 2 first
        </small>

    <?php endif; ?>

</div>
        

        <!-- Level 4 -->
<div class="training-level <?= $user['skill_level'] == 4 ? 'current' : ($user['skill_level'] > 4 ? 'completed' : 'locked'); ?>">

    <?php if ($user['skill_level'] >= 4): ?>

        <p>
            ✓ Level 4 — Melody Reproduction
        </p>

        <small>
            Test your melody skills
        </small>

    <?php else: ?>

        <p>
            🔒 Level 4 — Melody Reproduction
        </p>

        <small>
            Complete Level 3 first
        </small>

    <?php endif; ?>

</div>

    </div>


    <?php if ($user['skill_level'] >= 3): ?>

        <p>
            Test your musical ear and improve your skills!
        </p>


        <?php if ($user['skill_level'] == 3): ?>

            <a href="play.php">
                Start Level 3
            </a>

        <?php elseif ($user['skill_level'] >= 4): ?>

            <a href="level4/index.php">
                Start Level 4
            </a>

        <?php endif; ?>


    <?php else: ?>

        <p>
            You need to complete the first two quiz levels before unlocking Music Games.
        </p>

        <a href="../quiz/index.php">
            Go to Music Quiz
        </a>

    <?php endif; ?>

</div>


<?php

require_once "../includes/footer.php";

?>