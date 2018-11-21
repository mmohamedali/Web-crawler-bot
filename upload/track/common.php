<?php

if (!function_exists('getCurrentURI')) {
    function getCurrentURI() {

        $currentURI = 'http';

        if ($_SERVER["HTTPS"] == "on") {
            $currentURI .= "s";
        }
        
        $currentURI .= "://";
        
        if ($_SERVER["SERVER_PORT"] != "80") {
            $currentURI .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $currentURI .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }

        return $currentURI;
    }
}

if (!function_exists('getDomain')) {

    function getDomain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

}

?>