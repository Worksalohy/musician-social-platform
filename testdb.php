<?php
try {
    $pdo = new PDO("mysql:host=mysql;dbname=musician_social_platform", "root", "root");
    echo "Database connection successful!";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>