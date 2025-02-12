<?php

$base_url = 'https://www.test.hr';

echo "Container is up and running!";
echo "<br><br>";

if (!file_exists('/application/seen.json')) {
    echo "No seen.json file found!";
    die();
}

$seen = json_decode(file_get_contents('/application/seen.json'), true);
foreach ($seen as $value) {
    $link = $base_url . $value;
    echo "<a href='$link' target='_blank'>$link</a><br>";
}