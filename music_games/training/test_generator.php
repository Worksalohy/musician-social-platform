<?php

require_once "config.php";
require_once "generator.php";

$mode = 'diatonic';
$key = 'C#';
$difficulty = 'advanced';
$register = 4;

$challenge = generateChallenge(
    $mode,
    $key,
    $difficulty,
    $register
);

print_r($challenge);