<?php
session_start();

require_once "../config/db.php";
require_once "../middleware/auth.php";

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT id, username, avatar
    FROM users
    WHERE id != ?
    ORDER BY username
");

$stmt->execute([$user_id]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Chat</title>

<style>

body{
    font-family:Arial,sans-serif;
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

.search{
    width:100%;
    padding:10px;
    margin-bottom:20px;
    font-size:16px;
}

.user{
    display:flex;
    align-items:center;
    gap:15px;
    padding:12px;
    border-bottom:1px solid #ddd;
    text-decoration:none;
    color:#000;
}

.user:hover{
    background:#f8f8f8;
}

.avatar{
    width:50px;
    height:50px;
    border-radius:50%;
    object-fit:cover;
}

</style>

</head>

<body>

<div class="container">

<h2>Start New Chat</h2>

<input
    type="text"
    id="search"
    class="search"
    placeholder="Search users..."
>

<div id="userList">

<?php foreach($users as $user):

$avatar = "../assets/musicculture-default-avatar.png";

if(!empty($user['avatar'])){
    $avatar = "../".$user['avatar'];
}

?>

<a class="user"
   href="chat.php?user_id=<?= $user['id'] ?>">

    <img
        class="avatar"
        src="<?= htmlspecialchars($avatar) ?>"
        alt="avatar">

    <span><?= htmlspecialchars($user['username']) ?></span>

</a>

<?php endforeach; ?>

</div>

</div>

<script>

document
.getElementById("search")
.addEventListener("keyup", function(){

    const search = this.value.toLowerCase();

    document.querySelectorAll(".user")
    .forEach(function(user){

        const text = user.textContent.toLowerCase();

        if(text.includes(search)){
            user.style.display="flex";
        }else{
            user.style.display="none";
        }

    });

});

</script>

</body>
</html>