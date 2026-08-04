<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= $pageTitle ?? "MusicCulture"; ?></title>

    <link rel="stylesheet" href="/assets/css/main.css">

    <?php
    if (!empty($pageStyles)) {
        foreach ($pageStyles as $style) {
            echo '<link rel="stylesheet" href="' .
                htmlspecialchars($style) .
                '">' . PHP_EOL;
        }
    }
    ?>

</head>

<body>

<?php

require_once __DIR__ . '/navbar-data.php';
require_once __DIR__ . '/navbar.php';

?>

<div class="container">