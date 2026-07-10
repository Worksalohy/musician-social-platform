<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Messages</title>

<style>

body {
    font-family: Arial, sans-serif;
    background:#f4f4f4;
    padding:40px;
}

.container{
    max-width:700px;
    margin:auto;
    background:#fff;
    padding:20px;
    border-radius:10px;
}

.conversation{
    display:flex;
    align-items:center;
    gap:15px;
    padding:15px;
    border-bottom:1px solid #ddd;
    text-decoration:none;
    color:#000;
}

.conversation:hover{
    background:#f8f8f8;
}

.avatar{
    width:50px;
    height:50px;
    border-radius:50%;
    object-fit:cover;
}

.username{
    font-weight:bold;
}

.unread-badge{
    background:red;
    color:white;
    border-radius:50%;
    min-width:24px;
    height:24px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:12px;
    margin-left:auto;
}

</style>
</head>

<body>

<div class="container">

    <h2>Messages</h2>

    <div id="conversation-list"></div>

</div>

<script>

function loadConversations() {

    fetch("fetch-conversations.php")
        .then(response => response.text())
        .then(html => {
            document.getElementById("conversation-list").innerHTML = html;
        })
        .catch(error => console.error(error));
}

// Load immediately
loadConversations();

// Refresh every 3 seconds
setInterval(loadConversations, 3000);

</script>

</body>
</html>