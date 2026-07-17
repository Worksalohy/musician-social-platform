<?php

require_once "../config/db.php";
require_once "../middleware/auth.php";
require_once "../includes/header.php";

$user_id = $_SESSION['user_id'];

$search = trim($_GET['q'] ?? '');

$users = [];

if ($search !== '') {

    $stmt = $pdo->prepare("
        SELECT id, username, instrument, avatar
        FROM users
        WHERE id != ?
        AND (
            username LIKE ?
            OR instrument LIKE ?
        )
        ORDER BY username ASC
    ");

    $keyword = "%{$search}%";

    $stmt->execute([
        $user_id,
        $keyword,
        $keyword
    ]);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<style>

.search-container{

    max-width:700px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:10px;

}

.search-box{

    display:flex;
    gap:10px;
    margin-bottom:20px;

}

.search-box input{

    flex:1;
    padding:10px;
    font-size:16px;

}

.search-box button{

    padding:10px 15px;
    cursor:pointer;

}

.user-result{

    display:flex;
    align-items:center;
    gap:15px;
    padding:15px 0;
    border-bottom:1px solid #ddd;

}

.user-avatar{

    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;

}

.user-info h3{

    margin:0 0 5px;

}

.user-info a{

    text-decoration:none;

}

</style>


<div class="search-container">

<h2>Search Users</h2>


<form method="GET" class="search-box">

    <input
        type="text"
        name="q"
        placeholder="Search username or instrument..."
        value="<?= htmlspecialchars($search) ?>">

    <button type="submit">
        Search
    </button>

</form>


<?php if ($search !== ''): ?>


    <?php if (count($users) > 0): ?>


        <?php foreach ($users as $user): ?>


            <?php

            $avatar = "../assets/musicculture-default-avatar.png";

            if (!empty($user['avatar'])) {
                $avatar = "../" . $user['avatar'];
            }

            ?>


            <div class="user-result">


                <img
                    class="user-avatar"
                    src="<?= htmlspecialchars($avatar) ?>"
                    alt="Avatar">


                <div class="user-info">


                    <h3>
                        <?= htmlspecialchars($user['username']) ?>
                    </h3>


                    <p>
                        Instrument:
                        <?= htmlspecialchars($user['instrument'] ?? 'Not specified') ?>
                    </p>


                    <a href="../profile/profile.php?id=<?= $user['id'] ?>">
                        View Profile
                    </a>

                    |

                    <a href="../messages/chat.php?user_id=<?= $user['id'] ?>">
                        Message
                    </a>


                </div>


            </div>


        <?php endforeach; ?>


    <?php else: ?>


        <p>
            No users found.
        </p>


    <?php endif; ?>


<?php endif; ?>


</div>


<?php require_once "../includes/footer.php"; ?>