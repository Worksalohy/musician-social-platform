<?php

session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";


$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT skill_level, music_level
    FROM users
    WHERE id = ?
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);


?>


<div class="game-container">

    <h1>🎧 Music Training Game</h1>


    <p>
        Your current level:
        <strong>
            <?= htmlspecialchars($user['music_level']); ?>
        </strong>
    </p>


    <?php if ($user['skill_level'] >= 3): ?>

        <p>
            Test your musical ear and improve your skills!
        </p>


        <a href="play.php">
            Start Game
        </a>


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