<?php

require("config.php");
require("webhook.php");

date_default_timezone_set('Europe/Zagreb');

echo "Pinging Discord...\n";
sendToDiscord($webhook_ping, "rpi3b+ ping [".date(DATE_RFC2822)."]");
