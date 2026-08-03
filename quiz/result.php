<?php
session_start();

require_once "../middleware/auth.php";

if (
    !isset($_SESSION['quiz_score']) ||
    !isset($_SESSION['quiz_total'])
) {
    header("Location: index.php");
    exit;
}

$score = (int) $_SESSION['quiz_score'];
$total = (int) $_SESSION['quiz_total'];

$percentage = $total > 0 ? ($score / $total) * 100 : 0;

// Determine music level
if ($percentage >= 90) {
    $level = "🎼 Music Master";
} elseif ($percentage >= 75) {
    $level = "🎵 Advanced Musician";
} elseif ($percentage >= 50) {
    $level = "🎶 Intermediate Musician";
} else {
    $level = "🎧 Beginner";
}

require_once "../includes/header.php";

// Clear session values
unset($_SESSION['quiz_score']);
unset($_SESSION['quiz_total']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quiz Result</title>

<style>
.result-container{
    max-width:600px;
    margin:50px auto;
    background:#fff;
    padding:30px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
}

.score{
    font-size:42px;
    color:#28a745;
    margin:20px 0;
}

.level{
    font-size:24px;
    margin:20px 0;
    color:#333;
    font-weight:bold;
}

.play-again{
    display:inline-block;
    margin-top:20px;
    padding:12px 25px;
    background:#28a745;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
}

.play-again:hover{
    background:#218838;
}
</style>
</head>

<body>

<div class="result-container">

<h1>🎉 Quiz Completed!</h1>

<div class="score">
<?= $score ?> / <?= $total ?>
</div>

<p>
You scored <strong><?= number_format($percentage, 1) ?>%</strong>.
</p>

<div class="level">
Your Level: <?= htmlspecialchars($level) ?>
</div>

<a href="leaderboard.php" class="leaderboard-btn">
    🏆 View Leaderboard
</a>

<a class="play-again" href="index.php">
Play Again
</a>

</div>

</body>
</html>