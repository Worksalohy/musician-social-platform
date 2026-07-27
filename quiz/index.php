<?php
session_start();

require_once "../middleware/auth.php";
require_once "../includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Music Quiz</title>

<style>
.quiz-container{
    max-width:700px;
    margin:50px auto;
    background:#fff;
    padding:30px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
}

.quiz-container h1{
    margin-bottom:15px;
}

.quiz-container p{
    color:#666;
    margin-bottom:25px;
}

.start-btn{
    display:inline-block;
    padding:12px 25px;
    background:#28a745;
    color:white;
    text-decoration:none;
    border-radius:6px;
    font-size:18px;
}

.start-btn:hover{
    background:#218838;
}
</style>

</head>

<body>

<div class="quiz-container">

<h1>🎵 Music Quiz</h1>

<p>
Test your music knowledge and discover your level.
</p>

<a class="start-btn" href="play.php">
Start Quiz
</a>

</div>

</body>
</html>