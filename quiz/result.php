<?php
session_start();

require_once "../middleware/auth.php";
require_once "../includes/header.php";

if (!isset($_SESSION['quiz_score'])) {
    header("Location: index.php");
    exit;
}

$score = $_SESSION['quiz_score'];
$total = $_SESSION['quiz_total'];
$percentage = ($score / $total) * 100;

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

<a class="play-again" href="index.php">
Play Again
</a>

</div>

</body>
</html>