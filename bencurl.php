<?php

class bencurl
{

    public $url;
    protected $ch;

    function __construct($u)
    {
        $this->url = $this::fixencode($u);
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 7);
        #$userAgent =  UserAgent::random(['os_type' => "Windows", 'device_type' => "Desktop"]);
        $userAgent = 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    }

    protected function getit()
    {
        return curl_exec($this->ch);
    }

    public function getRawHeaders()
    {
        curl_setopt($this->ch, CURLOPT_NOBODY, true);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        return $this->getit();
    }


    public function getArrayOfRawHeaders()
    {
        preg_match_all('!(?<=\n\r\n|^)(.+?)(?=\n\r)!s', $this->getRawHeaders(), $m);
        return $m[1];
    }

    public function getArrayOfArrayOfHeaders()
    {
        return array_map(function ($headers) {
            preg_match_all('!(?<=\n|^)(.+?)(?=\n|$)!s', $headers, $m);
            return $m[1];
        }, $this->getArrayOfRawHeaders());
    }

    public function Headers()
    {
       return $this->getArrayOfArrayOfHeaders();
    }

    public static function fixencode($inp)
    {
        return  preg_replace_callback("![^[:ascii:]]!", function ($i) {
            return urlencode($i[0]);
        }, $inp);
    }
}


$x = new bencurl("https://google.com");



print_r($x->Headers());
#preg_match_all('!(?<=\n|^)(.+?)(?=\n|$)!s', $x->getarrayofrawheaders()[0], $m);
#echo ($m[1][1]);
