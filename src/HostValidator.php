<?php


namespace Rudl\IngressNginx;


use Rudl\LibGitDb\Type\Ingress\T_IngressConfig_Location;
use Rudl\LibGitDb\Type\Ingress\T_IngressConfig_VHost;

class HostValidator
{


    public static function convertUrlToHostAddr(string $input) : ?string
    {
        $chost = parse_url($input, PHP_URL_HOST);
        $cpath = parse_url($input, PHP_URL_PATH);
        if ($cpath == false)
            $cpath = "";
        $cport = parse_url($input, PHP_URL_PORT);
        if ($cport == false)
            $cport = "80";

        if ($chost === false)
            return null;
        $addr = gethostbyname($chost);
        if ( ! filter_var($addr, FILTER_VALIDATE_IP))
            return null;
        return "http://{$addr}:{$cport}{$cpath}";
    }

    public function validateUrl(string $url)
    {
        $host = parse_url($url, PHP_URL_HOST);

    }

    private function validateLocation (T_IngressConfig_Location $location)
    {

    }

    public function validate(T_IngressConfig_VHost $hostConfig)
    {
        foreach ($hostConfig->locations as $location) {

        }
    }

}