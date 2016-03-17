<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;


class FB extends Plugin{


	public $wa;
	public $pathFacebook;
	public $Configs;
	public $Groups;
	public $Permisos;
	public $Url;



	public function __construct(){
		$this->pathFacebook = __DIR__ . "/../library/facebook/";
		$this->Configs = array(
			"apiID" => "1448627002132119",
			"apiSecret" => "a48c663be820e365a0f6216d73799854",
			"url" => "http://powersensesystem.io/login/fb/",
			"session" => "facebook"
		);
		$this->Permisos = array(
			'email',
			'public_profile',
			'publish_actions',
			'user_groups',
			'publish_pages',
			'user_groups',
			'user_birthday',
			'user_about_me',
			
		);
		$this->Url = "";
		//require_once($this->pathFacebook . 'autoload.php');
		

		$this->pathFacebook = __DIR__ . "/../library/facebook/facebook/php-sdk-v4/src/Facebook/";
		$this->pathFacebook = __DIR__ . "/../library/facebook/facebook/php-sdk-v4/";

		require $this->pathFacebook . 'autoload.php' ;

		/*
		require_once( $this->pathFacebook . 'FacebookRedirectLoginHelper.php' );
		require_once( $this->pathFacebook . 'FacebookRequest.php' );
		require_once( $this->pathFacebook . 'FacebookResponse.php' );
		require_once( $this->pathFacebook . 'FacebookSDKException.php' );
		require_once( $this->pathFacebook . 'FacebookRequestException.php' );
		require_once( $this->pathFacebook . 'FacebookAuthorizationException.php' );
		require_once( $this->pathFacebook . 'GraphObject.php' );
		*/

		/*
  	use Facebook\FacebookSession;
    use Facebook\FacebookRequest;
    use Facebook\GraphUser;
    use Facebook\FacebookRequestException;
	*/

	}









	public function connect(){
		Facebook\FacebookSession::setDefaultApplication($this->Configs["apiID"] , $this->Configs["apiSecret"]);
		$facebook = new Facebook\FacebookRedirectLoginHelper($this->Configs["url"]);
		try {
			if($session = $facebook->getSessionFromRedirect()) {
				$_SESSION[$this->Configs["session"]] = $session->getToken();
				header('Location: ' . $this->Configs["url"]);
			}
		} catch(Facebook\FacebookRequestException $e){

		} catch(\Exception $e){

		}
		$this->Url = $facebook->getLoginUrl($this->Permisos);
	}

	public function getInfoUser(){
		Facebook\FacebookSession::setDefaultApplication($this->Configs["apiID"] , $this->Configs["apiSecret"]);
		$facebook = new Facebook\FacebookRedirectLoginHelper($this->Configs["url"]);		
		$session = new Facebook\FacebookSession($_SESSION[$this->Configs["session"]]);
		$request = new Facebook\FacebookRequest($session, 'GET', '/me');
		$request = $request->execute();
		$user = $request->getGraphObject()->asArray();
		return $user;
	}


	public function getGroups(){
		//http://lookup-id.com/
		//http://stackoverflow.com/questions/2821061/facebook-api-how-do-i-get-a-facebook-users-profile-image-through-the-facebook
		Facebook\FacebookSession::setDefaultApplication($this->Configs["apiID"] , $this->Configs["apiSecret"]);
		$facebook = new Facebook\FacebookRedirectLoginHelper($this->Configs["url"]);		
		$session = new Facebook\FacebookSession($_SESSION[$this->Configs["session"]]);
		$request = new Facebook\FacebookRequest($session, 'GET', '/me');
		$request = $request->execute();
		$Data = array("user" => array() , "groups" => array() , "errores" => array());
		$user = $request->getGraphObject()->asArray();
		$Data["user"] = $user;
		foreach($_REQUEST as $key => $value){
			if( strstr($key , "grupo_") ){
				try {
					$request = new Facebook\FacebookRequest(
					  $session,
					  'GET',
					  //'/'.trim($value).'/members?limit=5&offset=0'
					  '/'.trim($value).'/members?limit=1000000&offset=0'
					);
					$response = $request->execute();
					$arrayResult = json_decode($response->getRawResponse(), true);
					foreach($arrayResult["data"] as $_key => $_value){
						$Data["groups"][trim($value)][] = $_value;
					}
				} catch(Facebook\FacebookRequestException $e) {
					$Data["errores"][] = "Se encontro un error en el grupo " . trim($value);
					continue;
				} catch(\Exception $e) {
					$Data["errores"][] = "Se encontro un error en el grupo " . trim($value);
					continue;
				}
			}
		}
		return $Data;
	}









} 