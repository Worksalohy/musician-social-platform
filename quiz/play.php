<?php
session_start();

require_once "../middleware/auth.php";
require_once "../config/db.php";
require_once "../includes/header.php";

// Fetch all questions in random order
$stmt = $pdo->query("SELECT * FROM quiz_questions ORDER BY RAND()");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Play Music Quiz</title>

<style>

.quiz-container {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
}

.quiz-container h1 {
    text-align: center;
    margin-bottom: 30px;
}

.question {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

.question h3 {
    margin-bottom: 15px;
}

.option {
    margin: 10px 0;
}

.submit-btn {
    display: block;
    width: 100%;
    padding: 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
}

.submit-btn:hover {
    background: #218838;
}

</style>

</head>

<body>

<div class="quiz-container">

<h1>🎵 Music Quiz</h1>

<form action="submit.php" method="POST">

<?php if (count($questions) > 0): ?>

<?php foreach ($questions as $index => $question): ?>

<div class="question">

<h3>
Question <?= $index + 1; ?>
</h3>

<p><?= htmlspecialchars($question['question']); ?></p>

<div class="option">
<label>
<input
type="radio"
name="answers[<?= $question['id']; ?>]"
value="A"
required>

<?= htmlspecialchars($question['option_a']); ?>
</label>
</div>

<div class="option">
<label>
<input
type="radio"
name="answers[<?= $question['id']; ?>]"
value="B">

<?= htmlspecialchars($question['option_b']); ?>
</label>
</div>

<div class="option">
<label>
<input
type="radio"
name="answers[<?= $question['id']; ?>]"
value="C">

<?= htmlspecialchars($question['option_c']); ?>
</label>
</div>

<div class="option">
<label>
<input
type="radio"
name="answers[<?= $question['id']; ?>]"
value="D">

<?= htmlspecialchars($question['option_d']); ?>
</label>
</div>

</div>

<?php endforeach; ?>

<button class="submit-btn" type="submit">
Submit Quiz
</button>

<?php else: ?>

<p>No quiz questions available.</p>

<?php endif; ?>

</form>

</div>

</body>
</html>