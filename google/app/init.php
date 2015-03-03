<?php
session_start();
require_once 'vendor/autoload.php';

require_once 'classes/DB.php';

require_once 'classes/GoogleAuth.php';

$db = new DB;
$googleClient = new Google_Client;

$auth = new GoogleAuth($db, $googleClient);
?>