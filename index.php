<?php

session_start();
require('vendor/autoload.php');


var_dump($_SESSION);
unset($_SESSION['access_token']);
$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setAccessType("offline");        // offline access
$client->setIncludeGrantedScopes(true);   // incremental auth
$client->addScope(Google_Service_AdSense::ADSENSE_READONLY);
//$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth_callback.php');
$authUrl = $client->createAuthUrl();
$authUrl = filter_var($authUrl, FILTER_SANITIZE_URL)

?>


<a href='<?php echo $authUrl; ?>'>Login</a>