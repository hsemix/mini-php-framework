<?php
namespace DataFrame\Middleware\Helpers;
use DataFrame\Session;
use DataFrame\Database\Elegant\Model;
use DataFrame\Controllers\Controller;
class Middleware{
    private static $instances = array();
    public function __construct($args){
        if($args instanceof Session){
            return Session::instance();
        }
        if($args instanceof Model){
            return new $args;
        }
        if($args instanceof Controller){
            return Controller::instance();
        }
    }
    final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}
}