<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/header.php";

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

// Mark all messages from this user as read
$stmt = $pdo->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = ?
      AND receiver_id = ?
      AND is_read = 0
");

$stmt->execute([
    $other_user_id,
    $current_user_id
]);
?>

<style>

.chat-container{
    max-width:800px;
    margin:auto;
    background:#fff;
    padding:20px;
    border-radius:10px;
}

#messages-container{
    border:1px solid #ddd;
    border-radius:8px;
    padding:15px;
    height:450px;
    overflow-y:auto;
    margin-bottom:20px;
    background:#fafafa;
}

#message-form{
    display:flex;
    flex-direction:column;
    gap:10px;
}

#message{
    width:100%;
    min-height:80px;
    resize:vertical;
    padding:10px;
    font-size:16px;
    box-sizing:border-box;
}

#message-form button{
    align-self:flex-end;
    padding:10px 20px;
    cursor:pointer;
}

</style>

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

<script>

const form = document.getElementById('message-form');
const messagesContainer = document.getElementById('messages-container');

form.addEventListener('submit', function(e){

    e.preventDefault();

    const formData = new FormData(form);

    fetch('send-message.php',{
        method:'POST',
        body:formData
    })
    .then(response=>response.json())
    .then(data=>{

        if(data.success){

            form.reset();
            loadMessages();

        }else{

            alert("Failed to send message.");

        }

    })
    .catch(error=>console.error(error));

});

function loadMessages(){

    const userId =
        document.querySelector('[name="receiver_id"]').value;

    fetch('fetch-messages.php?user_id=' + userId)
        .then(response=>response.text())
        .then(html=>{

            messagesContainer.innerHTML = html;

            messagesContainer.scrollTop =
                messagesContainer.scrollHeight;

        });

}

// Initial load
loadMessages();

// Refresh every 3 seconds
setInterval(loadMessages, 3000);

</script>

<?php require_once "../includes/footer.php"; ?>