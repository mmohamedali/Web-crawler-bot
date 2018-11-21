<?php
class botaFramework {
    public $bridge;
    private $database;
    
    public function __construct($database = NULL) {

        // Check out database handle has been provided for us
        if (!$database) {
            throw new Exception('[ERROR] Invalid database handle passed to \bota\Bridge');
        } else {
            $this->database = $database;
        }        
        $this->bridge = new botaBridge($this->database);        
    }    
}

class botaBridge {

    private $database;    
    public function __construct($database = NULL) {

        // Check out database handle has been provided for us
        if (!$database) {
            throw new Exception('[ERROR] Invalid database handle passed to \bota\Bridge');
        } else {
            $this->database = $database;
        }
		include 'common.php';
    }

    public function log_access(array $botDetails) {
        if (empty($_SERVER['HTTP_REFERER'])) {
            $botDetails['referral'] = '';
            $botDetails['url']      = botaCommon::getCurrentURI();
        } else {
            $botDetails['referral'] = $_SERVER['HTTP_REFERER'];
            $botDetails['url']      = $_SERVER['HTTP_REFERER'];
        }
        $statement = $this->database->prepare('INSERT INTO `bot` (`visited_ip`, `visited_uri`,`visited_name`, `visited_by`, `visited_referral`) VALUES (INET_ATON(:ipaddress), :uri, :urname, :bot, :referral);');
        $statement->execute(array(':ipaddress' => $_SERVER['REMOTE_ADDR'], ':uri' => $botDetails['url'], ':urname' => basename($_SERVER['PHP_SELF']),  ':bot' => $botDetails['name'], ':referral' => $botDetails['referral']));
    }    
}


class botaCommon {    
    
    public static function getCurrentURI() {

        $currentURI = 'http';

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
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

    public static function getDomain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }   
}
?>
