<?php
namespace DataFrame\Database;
class DatabaseManager /*extends ArrayObject*/{
	public static $driver;
	public static $connection;
	public function setDriver($driver){
		static::$driver = $driver;
	}
	public static function connect(array $vars){
		if(static::$driver=="mysql"){
			static::$connection = new MySQLDatabase($vars);
		}elseif(static::$driver=="pgsql"){

		}elseif(static::$driver=="sqlite"){
			static::$connection = new SQLiteDatabase($vars);
		}
		
		return static::$connection;
	}

}
