<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MusicCulture</title>

<style>

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#f4f4f4;
}

.navbar{
    background:#222;
    color:#fff;
    padding:15px 30px;

    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
}

.logo{
    font-size:24px;
    font-weight:bold;
}

.nav-links{
    display:flex;
    align-items:center;
    gap:20px;
}

.nav-links a{
    color:#fff;
    text-decoration:none;
}

.nav-links a:hover{
    text-decoration:underline;
}

.search-form{
    display:flex;
    gap:8px;
}

.search-form input{
    padding:8px;
    width:220px;
}

.search-form button{
    padding:8px 14px;
    cursor:pointer;
}

.container{
    padding:30px;
}

.search-wrapper{
    position:relative;
}

#search-results{

    position:absolute;
    top:45px;
    left:0;
    width:250px;
    background:white;
    color:black;
    border-radius:5px;
    box-shadow:0 2px 8px rgba(0,0,0,.2);
    z-index:1000;

}

.search-item{

    display:flex;
    align-items:center;
    gap:10px;
    padding:10px;
    text-decoration:none;
    color:black;

}

.search-item:hover{

    background:#f2f2f2;

}

.search-item img{

    width:35px;
    height:35px;
    border-radius:50%;
    object-fit:cover;

}

</style>

</head>

<body>

<div class="navbar">

    <div class="logo">
        MusicCulture
    </div>

    <div class="nav-links">

        <a href="/dashboard/dashboard.php">Home</a>

        <a href="/profile/profile.php">
            Profile
        </a>

        <a href="/messages/inbox.php">
            Messages
        </a>

        <a href="/notifications/notifications.php">
            Notifications
        </a>

        <a href="/quiz/index.php">
            Music Quiz
        </a>


        <form
            class="search-form"
            action="/search/search.php"
            method="GET">

            <div class="search-wrapper">

                <input
                    id="global-search"
                    type="text"
                    name="q"
                    placeholder="Search musicians..."
                    autocomplete="off"
                    value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">

                <button type="submit">
                    Search
                </button>


                <div id="search-results"></div>

            </div>

        </form>

        <a href="/logout.php">
            Logout
        </a>

    </div>

</div>

<div class="container">