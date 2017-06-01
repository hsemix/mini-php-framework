<?php

namespace DataFrame;
spl_autoload_extensions(".php");
spl_autoload_register();
define('BASE_PATH', realpath(dirname(__FILE__)));
use Closure;
use Exception;
use DataFrame\Request;
use DataFrame\Session;
use DataFrame\Views\View;
use DataFrame\FileLocator;
use DataFrame\Views\SmxView;
use DataFrame\Database\ElegantManager\DatabaseManager;
include_once("global_funcs.php");
class Starter{
private static $instances = array();
	private $model_directory;
	private static $temp;
	private $controller_directory;
	private $appNamespace = null;
	private $subdir;
	public static function init(Closure $start){
		return $start(self::instance());
	}
	
	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
	
	public function setModelDir($dir){
		$model_directory = ($this->getAppNameSpace())?:'App';
		$model_directory = $model_directory."/".$dir;
		if (!file_exists($model_directory)){
			throw new Exception("The directory provided does not exist: $dir");
		}else{
			$appNamespace = ($this->getAppNameSpace())?:'App';
			$this->model_directory = $appNamespace."/".$dir;
			$this->getModelDir();
		}
	}
	
	public function getModelDir(){
		
		foreach (glob($this->model_directory."/*.php") as $filename){
		    include_once $filename;
		}
		$folder = explode("/",$this->model_directory);
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

		$model_directory = ($this->getAppNameSpace())?:'App';
		$model_directory = $model_directory."/".$dir;
		if (!file_exists($model_directory)){
			throw new Exception("The directory provided does not exist: $dir");
		}else{
			$appNamespace = ($this->getAppNameSpace())?:'App';
			$this->model_directory = $appNamespace."/".$dir;
			$this->getModelDir();
		}
		
	}

	public function setViewDir($dir){
		$view_directory = ($this->getAppNameSpace())?:'App';
		$view_directory = $view_directory."/".$dir;
		return $this->setTemplate(true,
				array(
					"tempDir" => $view_directory
				)
			);
	}



	
	public function getControllerDir(){
		$this->getModelDir();
		foreach (glob($this->model_directory."/*.php") as $filename){
		    include_once $filename;
		}
	}
	public function setDbConnect(array $args){
		$db = DatabaseManager::getInstance();
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
		$settings = parse_ini_file("config/config.ini");
		$url = '';
		$css = '';
		if(isset($settings['root'])){
			$url = $css = $settings['root']."/";
		}
		$self = static::instance();
		if($self->getAppNameSpace()){
			$url .= $self->getAppNameSpace()."/";
			$css = $css;
		}else{
			$url .= "App/";
			$css = $css;
		}

		
		
		$self = self::instance();
		$temp = $self::$temp;
		$temp->resource = $url;
		$temp->host = $css;
		return $temp;
	}

	public function setGlobals(array $args){
		$GLOBALS['config'] = $args;
		return $GLOBALS['config'];
	}

	public function setAppNameSpace($appNamespace){
		$this->appNamespace = $appNamespace;
	}

	public function getAppNameSpace(){
		return $this->appNamespace;
	}

	public static function boot(){
		self::init(function($args){
			$args->setGlobals([
				'session' => [
					'session_name' => 'user'
				]
			]);

			$args->setAppNameSpace('App'); 		# default app namespace
			$args->setModelDir("models"); 			# default models directory
			$args->setViewDir("views");				# default views directory
			$args->setControllerDir("controllers");	# default controllers directory
		});
	}

}

function app_loader($class_name){
	$parts = explode('\\', $class_name);
	$filename = BASE_PATH .'/' . str_replace('\\', '/', $class_name) . '.php';
	$camelClass = end($parts);
	$filename = str_replace($camelClass.'.php', strtolower($camelClass).'.php', $filename);
	if(file_exists($filename)){
		require_once($filename);
	}else{
		die("The file {$filename} could not be found.");
	}
}
spl_autoload_register("DataFrame\app_loader");

$session = Session::instance();

Starter::boot();