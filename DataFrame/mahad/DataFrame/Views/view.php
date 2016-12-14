<?php
namespace DataFrame\Views;
use DataFrame\Starter;
class View extends SmxView{
	private static $instances = array();
	private $ext = 'php';
	private $data = array();
	public function __construct($view, $extras = null, $ext = null){
		$starter = Starter::temp();
		if($ext){
			$this->ext = $ext;
		}
		$file = $view.'.'.$this->ext;

		if($extras){
			$this->data = $extras;
		}
		
		foreach($this->data as $var => $value){
			$starter->$var = $value;
		}
		return $starter->display($file);
	}

	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
}
