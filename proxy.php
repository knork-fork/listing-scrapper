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
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.83 Safari/537.36');
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

