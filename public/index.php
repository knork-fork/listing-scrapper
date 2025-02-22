<?php

use KnorkFork\LoadEnvironment\Environment;

require_once __DIR__ . '/../vendor/knorkfork/load-environment/src/Environment.php';

Environment::load(__DIR__ . '/../.env');

echo "Container is up and running!";
echo "<br><br>";

if (!file_exists('/application/seen.json')) {
    echo "No seen.json file found!";
    die();
}

$seen = json_decode(file_get_contents('/application/seen.json'), true);
foreach ($seen as $value) {
    $link = Environment::getStringEnv('BASE_URL') . $value;
    echo "<a href='$link' target='_blank'>$link</a><br>";
}