<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'] ?? null;

if (!$other_user_id) {
    die("User not found.");
}

$stmt = $pdo->prepare("
    SELECT id, username, avatar
    FROM users
    WHERE id = ?
");

$stmt->execute([$other_user_id]);
$other_user = $stmt->fetch();

if (!$other_user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat with <?= htmlspecialchars($other_user['username']) ?></title>
</head>
<body>

<h2>
    Chat with <?= htmlspecialchars($other_user['username']) ?>
</h2>

<div id="messages-container">

    <?php include "fetch-messages.php"; ?>

</div>

<form id="message-form" method="POST" action="send-message.php">
    <input type="hidden" name="receiver_id"
           value="<?= $other_user['id'] ?>">

    <textarea
        id="message"
        name="message"
        placeholder="Type a message..."
        required>
    </textarea>

    <button type="submit">
        Send
    </button>
</form>

<script>
const form = document.getElementById('message-form');
const messagesContainer = document.getElementById('messages-container');

form.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch('send-message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {

            // Clear the textarea
            form.reset();

            // Reload the messages
            loadMessages();

        } else {
            alert("Failed to send message.");
        }

    })
    .catch(error => {
        console.error(error);
    });
});

function loadMessages() {

    const userId = document.querySelector('[name="receiver_id"]').value;

    fetch('fetch-messages.php?user_id=' + userId)
        .then(response => response.text())
        .then(html => {

            messagesContainer.innerHTML = html;

            // Scroll to the bottom
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

        });
}

// Load messages when the page opens
loadMessages();
// Refresh the conversation every 3 seconds
setInterval(loadMessages, 3000);
</script>

</body>
</html>