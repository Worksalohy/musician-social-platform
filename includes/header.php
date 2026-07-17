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

        <form
            class="search-form"
            action="/search/index.php"
            method="GET">

            <input
                type="text"
                name="q"
                placeholder="Search musicians...">

            <button type="submit">
                Search
            </button>

        </form>

        <a href="/logout.php">
            Logout
        </a>

    </div>

</div>

<div class="container">