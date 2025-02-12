<?php

$base_url = 'https://www.test.hr';

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
    echo  $base_url.$s."\n";
}
