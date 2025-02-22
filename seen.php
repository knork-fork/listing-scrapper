<?php

use KnorkFork\LoadEnvironment\Environment;

require_once __DIR__ . '/vendor/knorkfork/load-environment/src/Environment.php';

Environment::load(__DIR__ . '/.env');

$seen = file_exists("seen.json") ? json_decode(file_get_contents("seen.json")) : [];

function cmp($a, $b)
{
    $a = explode("-",$a);
    $a = intval(end($a));
    $b = explode("-",$b);
    $b = intval(end($b));
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}

usort($seen, "cmp");

foreach ($seen as $s)
{
    echo Environment::getStringEnv('BASE_URL').$s."\n";
}
