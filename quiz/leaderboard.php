<?php
session_start();

$pageTitle = "Leaderboard | MusicCulture";
$currentPage = "leaderboard";

require_once "../middleware/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

// Get the best score of every user
$stmt = $pdo->query("
    SELECT
        u.id,
        u.username,
        u.avatar,
        MAX(q.percentage) AS best_percentage,
        MAX(q.score) AS best_score,
        MAX(q.total_questions) AS total_questions
    FROM users u
    INNER JOIN quiz_results q
        ON u.id = q.user_id
    GROUP BY u.id, u.username, u.avatar
    ORDER BY best_percentage DESC, best_score DESC
");

$leaders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>🏆 Music Quiz Leaderboard</h1>

<table border="1" cellpadding="10" cellspacing="0" width="100%">

<tr>
    <th>Rank</th>
    <th>Avatar</th>
    <th>Username</th>
    <th>Best Score</th>
    <th>Percentage</th>
    <th>Level</th>
</tr>

<?php

$rank = 1;

foreach ($leaders as $leader):

    // Medal system
    if ($rank == 1) {
        $displayRank = "🥇";
    } elseif ($rank == 2) {
        $displayRank = "🥈";
    } elseif ($rank == 3) {
        $displayRank = "🥉";
    } else {
        $displayRank = $rank;
    }


    // Determine level
    if ($leader['best_percentage'] >= 90) {
        $level = "Expert";
    } elseif ($leader['best_percentage'] >= 75) {
        $level = "Advanced";
    } elseif ($leader['best_percentage'] >= 50) {
        $level = "Intermediate";
    } else {
        $level = "Beginner";
    }

?>

<tr>

    <td>
        <?= $displayRank; ?>
    </td>


    <td>
        <img
            src="/<?= htmlspecialchars($leader['avatar']); ?>"
            width="50"
            height="50"
            style="
                border-radius:50%;
                object-fit:cover;
            ">
    </td>


    <td>
        <?= htmlspecialchars($leader['username']); ?>
    </td>


    <td>
        <?= $leader['best_score']; ?>/<?= $leader['total_questions']; ?>
    </td>


    <td>
        <?= number_format($leader['best_percentage'], 2); ?>%
    </td>


    <td>
        <?= $level; ?>
    </td>


</tr>

<?php

$rank++;

endforeach;

?>

</table>





</div>

</body>
</html>