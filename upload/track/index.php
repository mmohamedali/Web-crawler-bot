<?php
include 'config.php';
include 'framework.php';
try {
    $database = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $database->exec('SET CHARACTER SET utf8');
} catch (PDOException $e) {

    echo '[SQL Error] Unable to connect to server :: ' . $e->getMessage();
    die();
}
$frame = new botaFramework($database);
foreach ($defaultBots as $bot) {
    // Match the user agent against our bot mask list
    if (stripos($_SERVER['HTTP_USER_AGENT'], $bot['mask']) !== FALSE) {
        // If we have a Reverse DNS rule, make sure this request matches.
        if (!empty($bot['rdns'])) {
            if ($bot['rdns'] == botaCommon::getDomain('http://' . gethostbyaddr($_SERVER['REMOTE_ADDR']))) {
                $frame->bridge->log_access($bot);
            }else { $frame->bridge->log_access($bot); }
        } else {
            $frame->bridge->log_access($bot);
        }
    }
}
header('Content-type: image/gif');
echo file_get_contents('track.gif');
?>
