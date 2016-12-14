<?php
namespace DataFrame\Controllers;
use DataFrame\Universal;
class Controller{
	use Universal;
	public $login_id;
	public $req;
	public $res;
	private static $instances = array();
	public function __construct(){
		$this->login_id = $this->getLoggedInUserId();
		$this->req = $this->getRequest();
		$this->res = $this->getResponse();
	}

	public function getSession(){
		return $this->globalSession();
	}
	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
}