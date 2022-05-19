<?php


namespace Iapps\Ihg\Rpc;


class Rpc extends Service
{

    public $service;

    public function __construct($service = '')
    {
        $this->service = $service;
    }

    public function curl($url, $method = '', $header = [], $data = [], $referer = '', $cookie = '', $gzip = false, $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        if ($method == "POST") {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($gzip) {
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        }

        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);

        //echo $data;

        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'http_code' => $httpCode,
            'body'      => $data
        ];
    }


}
