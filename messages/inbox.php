<?php

$pageTitle = "Messages | MusicCulture";

$pageStyles = [
    "/assets/css/inbox.css"
];

$pageScripts = [
    "/assets/js/inbox.js"
];

$currentPage = "messages";

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/header.php";

?>

<div class="container">

    <div class="inbox-header">

        <h2>Messages</h2>

        <a href="new-chat.php" class="new-chat-btn">
            + New Chat
        </a>

    </div>


    <div id="conversation-list"></div>

</div>


<?php

require_once "../includes/footer.php";

?>