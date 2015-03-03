<?php

class GoogleAuth{
	private $db;
	private $client;
	
	public function __construct(DB $db, Google_Client $googleClient){
		
		$this->db = $db;
		$this->client = $googleClient;
		
		$this->client->setClientId('561145563891-pcj2la89s3iudm450fe2ehj8bmpo83jj.apps.googleusercontent.com');
		$this->client->setClientSecret('era8XXxL2OGsbBBTkuQfTeU7');
		$this->client->setRedirectUri('http://localhost/Usercake/google/index.php');
		$this->client->setScopes('email');
		
		//$this->storeUser($this->getPayLoad());
	}
	
	public function checkToken(){
		if(isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])){
			$this->client->setAccessToken($_SESSION['access_token']);
		}
		else{
			return $this->client->createAuthUrl();
		}
		return '';
	}	
	
	public function login(){
		if(isset($_GET['code'])){
			$this->client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $this->client->getAccessToken();
			$this->storeUser($this->getPayload());
			return true;
		}
		
		return false;
		
	} 
	
	public function logout(){
		unset($_SESSION['access_token']);
	}
	
	public function getPayLoad(){
		return json_decode(json_encode($this->client->verifyIdToken()->getAttributes()));
	}
	
	public function storeUser($payload){
		$sql = "INSERT INTO google_users(google_id, email) VALUES({$payload->id}, '{$payload->email}') ON DUPLICATE KEY UPDATE id = id";
		
		$this->db->query($sql);
	}
}

?>