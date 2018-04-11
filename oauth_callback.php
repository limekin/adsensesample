<?php

session_start();

require('vendor/autoload.php');

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setAccessType("offline");
$client->setIncludeGrantedScopes(true);
$client->addScope(Google_Service_AdSense::ADSENSE_READONLY);
$client->authenticate($_GET['code']);
$access_token = $client->getAccessToken();

$_SESSION['access_token'] = $access_token;

header('Location: data.php');

