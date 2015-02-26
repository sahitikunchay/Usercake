<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Prevent the user visiting the logged in page if he is not logged in
if(!isUserLoggedIn()) { header("Location: login.php"); die(); }

if(!empty($_POST))
{
	$errors = array();
	$successes = array();
		if($_FILES['fileToUpload']['name']==NULL){
		$password = $_POST["password"];
		$password_new = $_POST["passwordc"];
		$password_confirm = $_POST["passwordcheck"];
		$email = $_POST["email"];
	

$errors = array();	
	
	
	if($_POST["password"]!=NULL){
	$entered_pass = generateHash($password,$loggedInUser->hash_pw);
	
	if (trim($password) == "" ){
		$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
	}
	else if($entered_pass != $loggedInUser->hash_pw)
	{
		//No match
		$errors[] = lang("ACCOUNT_PASSWORD_INVALID");
	}	
	if($email != $loggedInUser->email)
	{
		if(trim($email) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_EMAIL");
		}
		else if(!isValidEmail($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		else if(emailExists($email))
		{
			$errors[] = lang("ACCOUNT_EMAIL_IN_USE", array($email));	
		}
		
		//End data validation
		if(count($errors) == 0)
		{
			$loggedInUser->updateEmail($email);
			$successes[] = lang("ACCOUNT_EMAIL_UPDATED");
		}
	}
	
	if ($password_new != "" OR $password_confirm != "" && $_FILES['fileToUpload']['name']=="")
	{
		if(trim($password_new) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_NEW_PASSWORD");
		}
		else if(trim($password_confirm) == "")
		{
			$errors[] = lang("ACCOUNT_SPECIFY_CONFIRM_PASSWORD");
		}
		else if(minMaxRange(8,50,$password_new))
		{	
			$errors[] = lang("ACCOUNT_NEW_PASSWORD_LENGTH",array(8,50));
		}
		else if($password_new != $password_confirm)
		{
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}
		
		//End data validation
		if(count($errors) == 0)
		{
			//Also prevent updating if someone attempts to update with the same password
			$entered_pass_new = generateHash($password_new,$loggedInUser->hash_pw);
			
			if($entered_pass_new == $loggedInUser->hash_pw)
			{
				//Don't update, this fool is trying to update with the same password Â¬Â¬
				$errors[] = lang("ACCOUNT_PASSWORD_NOTHING_TO_UPDATE");
			}
			else
			{
				//This function will create the new hash and update the hash_pw property.
				$loggedInUser->updatePassword($password_new);
				$successes[] = lang("ACCOUNT_PASSWORD_UPDATED");
			}
		}
	}
	if(count($errors) == 0 AND count($successes) == 0){
		$errors[] = lang("NOTHING_TO_UPDATE");
	}
}
	
		}
	//Perform some validation
	//Feel free to edit / change as required
	
	else {if($_FILES['fileToUpload']['name']!=""){
		$uploadOk=1;
		$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
		$detectedType = exif_imagetype($_FILES['fileToUpload']['tmp_name']);
		if( $_FILES['fileToUpload']['size'] > 2000000) {
			$errors[] = lang("FILE_SIZE_LARGE", array(2));
			$uploadOk = 0;
		}
		if(!in_array($detectedType, $allowedTypes)){
			$errors[] = lang("FILE_TYPE_NOT_ALLOWED");
			$uploadOk = 0;
		}
		$temp = explode(".", $_FILES["fileToUpload"]["name"]);
		$newname = dirname(__FILE__).'\\user-data\\img\\'.md5($loggedInUser->user_id).'.'.end($temp);
		if($uploadOk){
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $newname)) {
				$pic = md5($loggedInUser->user_id).'.'.end($temp);
				$loggedInUser->updatePic($pic);
				$successes[]= lang("PROFILE_PICTURE_CHANGED");
			} else {
				$errors[] = lang("PROFILE_PICTURE_ERROR");
			}
		}
	}
	}



	
	//Confirm the hashes match before updating a users password
	

}
require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>UserCake</h1>
<h2>User Settings</h2>
<div id='left-nav'>";
include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<div id='regbox'>
<form name='updateAccount' action='".$_SERVER['PHP_SELF']."' method='post'>
<p>
<label>Password:</label>
<input type='password' name='password' />
</p>
<p>
<label>Email:</label>
<input type='text' name='email' value='".$loggedInUser->email."' />
</p>
<p>
<label>New Pass:</label>
<input type='password' name='passwordc' />
</p>
<p>
<label>Confirm Pass:</label>
<input type='password' name='passwordcheck' />
</p>
<p>
<label>&nbsp;</label>
<input type='submit' value='Update' class='submit' />
</p>
</form>

    <p align = centre><br><br>
<label align = centre>Select image to upload:</label>
 <br><br>   <input type='file' name='fileToUpload' id='fileToUpload'><br><br></p><p>
 <br><br>   <input type=submit value=Upload Image name=submit></p>

</div>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";




?>
