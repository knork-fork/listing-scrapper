<?php

use KnorkFork\LoadEnvironment\Environment;

require_once __DIR__ . '/vendor/knorkfork/load-environment/src/Environment.php';
require("webhook.php");

Environment::load(__DIR__ . '/.env');

date_default_timezone_set('Europe/Zagreb');

echo "Pinging Discord...\n";
sendToDiscord(Environment::getStringEnv('DISCORD_WEBHOOK_PING'), "rpi3b+ ping [".date(DATE_RFC2822)."]");
