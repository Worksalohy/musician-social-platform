<?php

if (getenv("DB_HOST")) {
    $host = getenv("DB_HOST");
    $dbname = getenv("DB_NAME");
    $user = getenv("DB_USER");
    $pass = getenv("DB_PASSWORD");
} else {
    require __DIR__ . '/db.local.php';
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Unable to connect to the database.");
}
