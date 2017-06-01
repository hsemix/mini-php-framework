<?php
namespace DataFrame\Database\ElegantManager;
use DataFrame\Database\Drivers\MySQLDatabase;
use DataFrame\Database\Drivers\SQLiteDatabase;
class DatabaseManager{
	public static $driver;
	public static $connection;
	private static $instances = [];
	public function setDriver($driver){
		static::$driver = $driver;
	}
	public static function connect(array $vars){
		if(static::$driver=="mysql"){
			//static::$connection = new MySQLDatabase($vars);
			$connection = MySQLDatabase::getInstance();
			static::$connection = $connection->setConnection($vars);
		}elseif(static::$driver=="pgsql"){

		}elseif(static::$driver=="sqlite"){
			static::$connection = new SQLiteDatabase($vars);
		}
		
		return static::$connection;
	}

	public static function getInstance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}

}
