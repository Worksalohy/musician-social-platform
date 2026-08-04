<div class="navbar">

    <div class="logo">
        <a href="/dashboard/dashboard.php">
            🎵 MusicCulture
        </a>
    </div>

    <div class="nav-links">

        <a
    href="/dashboard/dashboard.php"
    class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
    Home
</a>

<a
    href="/profile/profile.php"
    class="<?= ($currentPage ?? '') === 'profile' ? 'active' : '' ?>">
    Profile
</a>

<a
    href="/messages/inbox.php"
    class="<?= ($currentPage ?? '') === 'messages' ? 'active' : '' ?>">

    Messages

    <?php if (!empty($unreadMessages) && $unreadMessages > 0): ?>

        <span class="badge">
            <?= $unreadMessages ?>
        </span>

    <?php endif; ?>

</a>

<a
    href="/notifications/notifications.php"
    class="<?= ($currentPage ?? '') === 'notifications' ? 'active' : '' ?>">

    Notifications

    <?php if (!empty($unreadNotifications) && $unreadNotifications > 0): ?>

        <span class="badge">
            <?= $unreadNotifications ?>
        </span>

    <?php endif; ?>

</a>

<a
    href="/quiz/index.php"
    class="<?= ($currentPage ?? '') === 'quiz' ? 'active' : '' ?>">
    Music Quiz
</a>

<a
    href="/quiz/leaderboard.php"
    class="<?= ($currentPage ?? '') === 'leaderboard' ? 'active' : '' ?>">
    Leaderboard
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
                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">

                <button type="submit">
                    Search
                </button>

                <div id="search-results"></div>

            </div>

        </form>

        <a class="logout-link" href="/auth/logout.php">
            Logout
        </a>

    </div>

</div>