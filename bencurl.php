<?php

class bencurl
{

    public $url;
    protected $ch;

    public function bencurl_setopt($opt,$param=null)
    {
        curl_setopt($this->ch, $opt, $param);
    }


    function __construct($u)
    {
        $this->url = $this::fixencode($u);
        $this->ch = curl_init();
        $this->bencurl_setopt(CURLOPT_URL, $this->url);
        $this->bencurl_setopt(CURLOPT_RETURNTRANSFER, 1);
        $this->bencurl_setopt(CURLOPT_FOLLOWLOCATION, 1);
        $this->bencurl_setopt(CURLOPT_SSL_VERIFYHOST, 0);
        $this->bencurl_setopt(CURLOPT_SSL_VERIFYPEER, 0);
        $this->bencurl_setopt(CURLOPT_CONNECTTIMEOUT, 5);
        $this->bencurl_setopt(CURLOPT_TIMEOUT, 7);
        #$userAgent =  UserAgent::random(['os_type' => "Windows", 'device_type' => "Desktop"]);
        $userAgent = 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
        $this->bencurl_setopt(CURLOPT_USERAGENT, $userAgent);
    }



    protected function getit()
    {
        return curl_exec($this->ch);
    }

    
    public function fileSize()
    {
        return curl_getinfo(CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }

    public function getRawHeaders()
    {
        $this->bencurl_setopt(CURLOPT_NOBODY, true);
        $this->bencurl_setopt(CURLOPT_HEADER, 1);
        return $this->getit();
    }

    public function download($filename=null)
    {
      
        $this->bencurl_setopt(CURLOPT_NOBODY, false);
        $this->bencurl_setopt(CURLOPT_HEADER, 0);

        if ($filename) {
            $this->bencurl_setopt(CURLOPT_FILE, fopen ($filename, 'w+')); 
        }
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

