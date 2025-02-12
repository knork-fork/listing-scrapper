<?php

echo "Initializing...\n";
require __DIR__ . '/vendor/autoload.php';
require("proxy.php");
require("config.php");
require("webhook.php");

use Sunra\PhpSimple\HtmlDomParser;

date_default_timezone_set('Europe/Zagreb');

$proxy = new Proxy();
$seen = file_exists("seen.json") ? json_decode(file_get_contents("seen.json")) : [];
$base_url = 'https://www.test.hr';
$url = $base_url . $request;
$page = 1;
$new = 0;
$newText = "";

echo "Starting...\n";

do
{
    $url_r = $url . ($page > 1 ? "&page=$page" : "");
    $html = $proxy->curl($url_r);

    $curlOutputFilename = sprintf(
        'curl_output_%s.html',
        bin2hex(random_bytes(8))
    );
    $curlOutputPath = '/application/public/' . $curlOutputFilename;
    $curlOutputUrl = 'http://localhost:35000/' . $curlOutputFilename;

    if (strstr($html, "302 Found"))
    {
        $url = $proxy->curl($url_r, true);
        file_put_contents($curlOutputPath, $html);
        sendToDiscord($webhook, "$tag_myself Script died, `302 Found` returned!\n[".date(DATE_RFC2822)."]\nSee output: $curlOutputUrl");
        die();
    } elseif (strstr($html, "validate.perfdrive.com/") && strstr($html, "/captcha")) {
        file_put_contents($curlOutputPath, $html);
        sendToDiscord($webhook, "$tag_myself Proxy failed, captcha triggered!!!\n[".date(DATE_RFC2822)."]\nSee output: $curlOutputUrl");
        die();
    } elseif (strstr($html, "Error 400")) {
        file_put_contents($curlOutputPath, $html);
        sendToDiscord($webhook, "$tag_myself Script died, `Error 400 (Bad Request)` returned!\n[".date(DATE_RFC2822)."]\nSee output: $curlOutputUrl");
        die();
    } else {
        sendToDiscord($webhook_ping, "Normal response returned [".date(DATE_RFC2822)."]");
    }
    $html = HtmlDomParser::str_get_html($html);
    $i = 0;
    $keep_running = false;

    foreach ($html->find('li.EntityList-item') as $stan)
    {
        if (!strstr($stan->class,"EntityList-item--VauVau") && !strstr($stan->class,"EntityList-item--Regular")) continue;

        $datetime = $stan->find("time.date");
        foreach ($datetime as $d)
        {
            // Find only those posted today, otherwise stop
            if (isset($d->attr["datetime"]))
            {
                $keep_running = (date("j-n-Y",strtotime($d->attr["datetime"])) == date("j-n-Y"));
            }
        }

        if (isset($stan->attr["data-href"]) &&
            strstr($stan->attr["data-href"],'/nekretnine/') &&
            !in_array($stan->attr["data-href"], $seen))
        {
            $keep_running = true;
            $seen []= $stan->attr["data-href"];
            $newText .= $base_url . $stan->attr["data-href"] . "\n";
            $new++;
        }
    }
    $page ++;
    // It can't handle more than 2-3 connections...
    if ($page > 2)
        break;
    sleep(rand(5,13));

} while ($keep_running);

file_put_contents("seen.json", json_encode($seen, JSON_PRETTY_PRINT));

echo "\e[1;33mDone, found $new new. \nRun \e[0;32mphp seen.php\e[1;33m to get a full list.\e[0m\n";
sendToDiscord($webhook_ping, "Found $new new items [".date(DATE_RFC2822)."]");

if ($new > 0)
{
    echo "Sending to Discord...\n";

    // 5 is the limit of embeds
    if ($new > 5) {
        $groups = array_chunk(explode("\n", $newText), 5);
        foreach ($groups as $group) {
            sendToDiscord($webhook, "New stuff [".date(DATE_RFC2822)."]:\n" . implode("\n", $group));
        }
    } else {
        sendToDiscord($webhook, "New stuff [".date(DATE_RFC2822)."]:\n".$newText);
    }
}