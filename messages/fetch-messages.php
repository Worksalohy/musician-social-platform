<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/message-functions.php";


$current_user_id = (int) $_SESSION['user_id'];

$other_user_id = isset($_GET['user_id'])
    ? (int) $_GET['user_id']
    : 0;


if ($other_user_id <= 0) {
    exit;
}


// Mark messages as read
markConversationAsRead(
    $pdo,
    $other_user_id,
    $current_user_id
);


// Fetch conversation
$messages = getConversationMessages(
    $pdo,
    $current_user_id,
    $other_user_id
);


if (empty($messages)) {

    echo "<p>No messages yet. Start the conversation!</p>";

    exit;
}


foreach ($messages as $message):

?>

<div class="message">

    <strong>

        <?= $message['sender_id'] == $current_user_id
            ? "You"
            : htmlspecialchars($message['username']) ?>

    </strong>


    <p>
        <?= nl2br(
            htmlspecialchars($message['message'])
        ) ?>
    </p>


    <small>
        <?= htmlspecialchars($message['created_at']) ?>
    </small>

</div>

<?php endforeach; ?>