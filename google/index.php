<?php

require_once 'app/init.php';

$db = new DB;
$googleClient = new Google_Client;

$auth = new GoogleAuth($db, $googleClient);

$authUrl= $auth->checkToken(); //change later
//$authUrl = 1;
if($auth->login()){
	$redirect = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	header('Location:'. filter_var($redirect, FILTER_SANITIZE_URL));
	//echo "ok";
	}
	
	//if(!$authUrl){
		//echo'<pre>', print_r($auth->getPayload()), '</pre>';
	//}

?>

<!doctype html>
<html>
	<head>
		<meta charset ="utf-8">
		<title>Your Website</title>
		</head>
		<body>
			<?php if($authUrl): ?>
				
				<a href ="<?=$authUrl?>">Sign in with google.</a>
			<?php else: ?>
				You are logged in.<a href="logout.php">Log out</a>
			<?php endif;?>
			</body>
		</html>