<?php

use KnorkFork\LoadEnvironment\Environment;

require_once __DIR__ . '/vendor/knorkfork/load-environment/src/Environment.php';

class Proxy
{
    private const DEFAULT_PROXY = 'localhost:9050';

	private $ch, $proxy;

	function __construct()
    {
        Environment::load(__DIR__ . '/.env');

        if (Environment::getStringEnv('PROXY') !== self::DEFAULT_PROXY) {
            // Use third party proxy
            $this->proxy = Environment::getStringEnv('PROXY');
        } else {
            // Use local tor proxy
            $this->proxy = self::DEFAULT_PROXY;
            system("(echo authenticate '\"\"'; echo signal newnym; echo quit) | nc localhost 9051 > /dev/null 2>&1");
            sleep(15);
        }

        $this->ch = curl_init();

        // this 'should' work
        // sometimes it throws a captcha, follow /var/www/html/curl_output.html
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
        curl_setopt($this->ch, CURLOPT_USERAGENT, Environment::getStringEnv('USER_AGENT'));
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1'
        ]);
    }

    public function curl( $url, $get_location_url = null )
    {
        curl_setopt( $this->ch, CURLOPT_URL, $url );

        if ($get_location_url)
        {
            curl_setopt( $this->ch, CURLOPT_HEADER, true );
            $http_data = curl_exec($this->ch);
            $curl_info = curl_getinfo($this->ch);
            $headers = substr($http_data, 0, $curl_info["header_size"]);
            preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $headers, $matches);
            return $matches[1];
        }

        $response = curl_exec($this->ch);

        if (curl_errno($this->ch))
        {
            return 'Curl error: ' . curl_error($this->ch);
        }

        return $response;
    }

    function __destruct()
    {
        curl_close( $this->ch );
    }
}

