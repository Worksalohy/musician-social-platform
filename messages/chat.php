<?php

$pageTitle = "Chat | MusicCulture";

$pageStyles = [
    "/assets/css/chat.css"
];

$pageScripts = [
    "/assets/js/chat.js"
];

$currentPage = "messages";

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/message-functions.php";
require_once "../includes/header.php";

$current_user_id = (int) $_SESSION['user_id'];

$other_user_id = isset($_GET['user_id'])
    ? (int) $_GET['user_id']
    : 0;

if ($other_user_id <= 0 || $other_user_id === $current_user_id) {
    die("Invalid conversation.");
}

if ($other_user_id <= 0) {
    die("User not found.");
}

$other_user = getChatUser(
    $pdo,
    (int) $other_user_id
);

if (!$other_user) {
    die("User not found.");
}

markConversationAsRead(
    $pdo,
    (int) $other_user_id,
    $current_user_id
);
?>

<div class="chat-container">

    <h2>
        Chat with <?= htmlspecialchars($other_user['username']) ?>
    </h2>

    <div id="messages-container">

        <?php include "fetch-messages.php"; ?>

    </div>

    <form id="message-form" method="POST" action="send-message.php">

        <input
            type="hidden"
            name="receiver_id"
            value="<?= $other_user['id'] ?>">

        <textarea
            id="message"
            name="message"
            placeholder="Type a message..."
            required></textarea>

        <button type="submit">
            Send
        </button>

    </form>

</div>

<?php require_once "../includes/footer.php"; ?>