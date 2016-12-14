<?php
	//include_once("database.php");
	//include_once("connections.php");
	namespace DataFrame\Database;
	class MySQLDatabase extends Database{
		use Connections;
		private $DB_NAME;
		private $DB_PASS;
		private $DB_SERVER;
		private $DB_USER;
		private $mysql_result;
		function __construct($dbvars){
			$this->DB_NAME = $dbvars['dbname'];
			$this->DB_PASS = $dbvars['dbpass'];
			$this->DB_SERVER = $dbvars['dbserver'];
			$this->DB_USER = $dbvars['dbuser'];
			$this->open_connection();
			$this->database_type = "mysql";
			$this->magic_quotes_active = get_magic_quotes_gpc();
			$this->mysql_real_escape_string_exists = function_exists("mysql_real_escape_string");
		}
		public function open_connection(){
			
			try{
				$this->connection = new \PDO('mysql:host='.$this->DB_SERVER.';dbname='.$this->DB_NAME, $this->DB_USER,$this->DB_PASS);
				$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}catch(\PDOException $ex){
				header("Location: ../reload.html");
				die("Database selection failed: ". $ex->getMessage());
			}
		}
		public function query($sql){
			$this->last_query = $sql;
			try{
				$result = $this->connection->query($sql);
				return $result;
			}catch(\PDOException $ex){
				$output = "Database query failed: " .$ex->getMessage() ."<br /><br />";
				$output .= "Last SQL query: ".$this->last_query;
				die($output);
			}
		}
		public function escape_value($value){
			return $value;
		}
		public function lastInsertId(){
			return $this->connection->lastInsertId();
		}
	}