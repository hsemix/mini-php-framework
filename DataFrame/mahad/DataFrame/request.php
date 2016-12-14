<?php
namespace DataFrame;
class Request {
	const GET = 'GET';
	const POST = 'POST';
	private $domain;
	private $path;
	private $method;
	private $params;
	private $cookies;
	private static $instances = array();
	public function __construct($method = null) {
		$settings = parse_ini_file("/config/config.ini");
		$this->domain = $_SERVER['HTTP_HOST'];
		$path = isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI'] : '/';
		if(isset($settings['root'])){
			$this->path = explode('?', str_replace("/".$settings['root'], "", $path))[0];
		}else{
			$this->path = explode('?', $path)[0];
		}
		//$this->path = explode('?', str_replace("/".$this->getSubfolder(), "", $path))[0];
		
		if($method){
			$this->method = $method;
		}else{
			$this->method = $_SERVER['REQUEST_METHOD'];
		}
		
		$this->params = new FilteredMap(array_merge($_POST, $_GET, $_FILES));
		$this->cookies = new FilteredMap($_COOKIE);
		if($this->params->getString('_method')){
			$this->method = $this->params->getString('_method');
		}
		//print_r($this);
	}
	public function setSubfolder($folder){
		$this->subfolder = $folder;
	}
	public function getSubfolder(){
		return (string)$this->subfolder;
	}
	public function getUrl(){
		return (string)$this->domain . $this->path;
	}
	public function getDomain(){
		return (string)$this->domain;
	}
	public function getPath(){
		return (string)$this->path;
	}

	public function getMethod() {
		return (string)$this->method;
	}
	public function isPost() {
		return (bool)$this->method === self::POST;
	}
	public function isGet() {
		return (bool)$this->method === self::GET;
	}
	public function getParams() {
		return $this->params;
	}
	public function getCookies(){
		return $this->cookies;
	}
	public function __call($method, $args) {
		$class = new \ReflectionClass('DataFrame\FilteredMap');
		if($class->hasMethod($method)){
			return $this->getParams()->$method($args[0]);
		}else{
			die("Mahad\DataFrame Or Your App misses a Class with method ".$method);
		}
	}
	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
	public function isAjax(){
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
			return true;
		}else{
			return false;
		}
	}
}