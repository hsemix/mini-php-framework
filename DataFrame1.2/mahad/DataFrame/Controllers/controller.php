<?php
namespace DataFrame\Controllers;
use DataFrame\Universal;
use DataFrame\Token;
use DataFrame\Request;
use DataFrame\Response;
use DataFrame\Session;
use DataFrame\Validate;
class Controller{
	use Universal;
	public $login_id;
	public $req;
	public $res;
	public $token;
	public $validate;
	private static $instances = [];
	public function __construct(Request $req = null, Response $res = null, Session $session = null, Token $token = null, Validate $validate = null){
		
		$this->login_id = ($this->getLoggedInUserId())?:null;

		$this->req = $req;
		$this->res = $res;
		$this->session = $session;
		$this->token = $token;
		$this->validate = $validate;
		if($req == null){
			$this->req = request();
		}
		if($res == null){
			$this->res = response();
		}
		if($session == null){
			$this->session = Session::instance();
		}

		if($token == null){
			$this->token = token();
		}

		if($validate == null){
			$this->validate = new Validate();
		}
	}

	public function getSession(){
		return ($this->globalSession())?:null;
	}
	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
}