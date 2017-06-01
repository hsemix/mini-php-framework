<?php
namespace DataFrame;
use DataFrame\Database\Elegant\Model;
class Session{
	private $logged_in = FALSE;
	public $user_id;
	private static $instances = array();
	function __construct(){
		if(session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		$this->check_login();
	}
	public function is_logged_in(){
		
		
		if(self::exists(getGlobals('session.session_name')) && self::get(getGlobals('session.session_name'))){
			$this->logged_in = TRUE;
		}
		return $this->logged_in;
	}
	
	public function login($user = null){
		
		if($user && $user instanceof Model){
			self::put(getGlobals('session.session_name'), $user->{$user->getPrimaryKey()});
			$this->user_id = self::get(getGlobals('session.session_name'));
			$this->logged_in = true;
		}
	}
	
	public function logout(){
		self::delete(getGlobals('session.session_name'));
		//unset($this->user_id);
		$this->logged_in = false;
	}

	private function check_login(){
		if(self::exists(getGlobals('session.session_name'))){
			$this->user_id = self::get(getGlobals('session.session_name'));
			$this->logged_in = true;
		}else{
			//unset($this->user_id);
			$this->logged_in = false;
		}
	}

	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}

	public static function put($name, $value){
		return $_SESSION[$name] = $value;
	}
	public static function exists($name){
		return (isset($_SESSION[$name])) ? true : false;
	}

	public static function delete($name){
		if(self::exists($name)){
			unset($_SESSION[$name]);
		}
	}

	public static function get($name){
		return $_SESSION[$name];
	}
	public static function flush($name, $string=""){
		if(self::exists($name)){
			$session = self::get($name);
			self::delete($name);
			return $session;
		}else{
			self::put($name, $string);
		}
	}
}
$session = Session::instance();
