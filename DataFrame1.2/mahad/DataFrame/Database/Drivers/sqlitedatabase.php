<?php
namespace DataFrame\Database\Drivers;
use DataFrame\Database\Support\Connections;
use DataFrame\Database\Support\Helpers\Database;
class SQLiteDatabase extends Database{
	use Connections;
	private $DB_LOCATION;
	function __construct($dbvars){
		$this->DB_LOCATION = $dbvars['dblocation'];
		$this->open_connection();
		$this->database_type = "sqlite";
		$this->magic_quotes_active = get_magic_quotes_gpc();
		$this->mysql_real_escape_string_exists = function_exists("mysql_real_escape_string");
	}
	public function open_connection(){
		try{
			$this->connection = new PDO("sqlite:".$this->DB_LOCATION);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $ex){
			die($ex->getMessage());
		}
	}
	public function query($sql){
		$this->last_query = $sql;
		try{
			$result = $this->connection->query($sql);
			return $result;
		}catch(PDOException $ex){
			$output = "Database query failed: " .$ex->getMessage() ."<br /><br />";
			$output .= "Last SQL query: ".$this->last_query;
			die($output);
		}
	}
	public function escape_value($value){
		if($this->mysql_real_escape_string_exists){
			if($this->magic_quotes_active){$value=stripslashes($value);}
		}else{
			if(!$this->magic_quotes_active){$value = addcslashes($value);}
		}
		return $value;
	}
	public function lastInsertId(){
		return $this->connection->lastInsertId();
	}
}
