<?php

class Proxy
{
	private $ch, $proxy;

	function __construct()
    {
        system("(echo authenticate '\"\"'; echo signal newnym; echo quit) | nc localhost 9051 > /dev/null 2>&1");
        sleep(15);

        $this->ch = curl_init();

        // this 'should' work
        // sometimes it throws a captcha, follow /var/www/html/curl_output.html
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($this->ch, CURLOPT_PROXY, "localhost:9050");
        curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Version/17.1 Safari/537.36');
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

        return curl_exec( $this->ch );
    }

    function __destruct()
    {
        curl_close( $this->ch );
    }
}

