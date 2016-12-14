<?php
namespace DataFrame;
use DataFrame\Database\DatabaseManager;
use DataFrame\Views\SmxView;
use DataFrame\Views\View;
use DataFrame\Session;
use DataFrame\FileLocator;
use DataFrame\Request;

class Starter{
	private static $instances = array();
	private $model_directory;
	private static $temp;
	private $controller_directory;
	private $appNamespace;
	private $subdir;
	public static function init(\Closure $start){
		return $start(self::instance());
	}
	
	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
	
	public function setModelDir($dir){
		if (!file_exists($dir)){
			throw new \Exception("The directory provided does not exist: $dir");
		}else{
			$this->model_directory = $dir;
			$this->getModelDir();
		}
	}
	
	public function getModelDir(){
		foreach (glob($this->model_directory."/*.php") as $filename){
		    include_once $filename;
		}
		$folder = split("/",$this->model_directory);
		if(count($folder) >= 2){
			if(!file_exists($folder[0]."/middleware")){

			}else{
				foreach (glob($folder[0]."/middleware/*.php") as $filename){
					include_once $filename;
				}
			}
		}else{
			if(!file_exists("middleware")){

			}else{
				foreach (glob("middleware/*.php") as $filename){
					include_once $filename;
				}
			}
		}
	}

	public function setControllerDir($dir){
		if (!file_exists($dir)){
			throw new \Exception("The directory provided does not exist: $dir");
		}else{
			$this->model_directory = $dir;
			$this->getModelDir();
		}
	}



	
	public function getControllerDir(){
		$this->getModelDir();
		foreach (glob($this->model_directory."/*.php") as $filename){
		    include_once $filename;
		}
	}
	public function setDbConnect(array $args){
		$db = new DatabaseManager();
		$db->setDriver($args['dbtype']);
		$db->connect($args);
		return $db;
	}
	public function setTemplate($type = false, array $args){
		if($type == true){
			$smxview = new SmxView($args['tempDir']."/");
			self::$temp = $smxview;
		}
	}
	public static function temp(){
		$settings = parse_ini_file("/config/config.ini");
		$url = '';
		$css = '';
		if(isset($settings['root'])){
			$url = $css = $settings['root']."/";
		}

		if(isset($settings['appfolder'])){
			$url .= $settings['appfolder']."/";
			$css = $css;
		}
		
		$self = self::instance();
		$temp = $self::$temp;
		$temp->resource = $url;
		$temp->host = $css;
		return $temp;
	}

}


function app_loader($class_name){
	$path = dirname(realpath(__FILE__))."/{$class_name}.php"; #very important line, it shouldn't miss in an autoload function
	if(file_exists($path)){
		require_once($path);
	}else{
		die("The file {$class_name}.php could not be found.");
	}
}
spl_autoload_register("DataFrame\app_loader");

$session = Session::instance();