<?php

function sendToDiscord($webhook, $content)
{
    $hookObject = json_encode([
        /*
         * The general "message" shown above your embeds
         */
        "content" => $content,
        /*
         * The username shown in the message
         */
        "username" => "NjuškaloScrapper",
        /*
         * Whether or not to read the message in Text-to-speech
         */
        "tts" => false,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    $ch = curl_init( $webhook );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $hookObject);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    curl_close( $ch );
}