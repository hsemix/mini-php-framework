<?php
	namespace DataFrame\Database\Drivers;
	use PDO;
	use PDOException;
	use DataFrame\Database\Support\Connections;
	use DataFrame\Database\Support\Helpers\Database;
	class MySQLDatabase extends Database{
		use Connections;
		private $DB_NAME;
		private $DB_PASS;
		private $DB_SERVER;
		private $DB_USER;
		private $mysql_result;
		public static $instances = [];
		function __construct($dbvars = null){
			$this->database_type = "mysql";
		}

		final public static function getInstance(){
			$class_name = get_called_class();

			if (!isset(self::$instances[$class_name]))
				self::$instances[$class_name] = new $class_name;

			return self::$instances[$class_name];
		}

		public function setConnection($dbvars){
			$this->DB_NAME = $dbvars['dbname'];
			$this->DB_PASS = $dbvars['dbpass'];
			$this->DB_SERVER = $dbvars['dbhost'];
			$this->DB_USER = $dbvars['dbuser'];
			$this->open_connection();
			return $this;
		}
		public function open_connection(){
			
			try{
				$this->connection = new PDO('mysql:host='.$this->DB_SERVER.';dbname='.$this->DB_NAME, $this->DB_USER,$this->DB_PASS);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $ex){
				header("Location: ../reload.html");
				die("Database selection failed: ". $ex->getMessage());
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
			return $value;
		}
		public function lastInsertId(){
			return $this->connection->lastInsertId();
		}

		public function execute($bindings, $pdo){
			try{
				if(!is_null($bindings)){
						if(!is_array(reset($bindings))){
						$bindings = [$bindings];
					}
					foreach($bindings as $binding){
						$pdo->execute($binding);
					}
				}else{
					$pdo->execute();
				}
				

				
				return $pdo;
				
			}catch(PDOException $ex){
				$output = "Database query failed: " .$ex->getMessage() ."<br /><br />";
				$output .= "Last SQL query: ".$this->last_query;
				die($output);
			}
		}

		public function pdoQuery($sql, $bindings){
			$this->last_query = $sql;
			$query = $this->connection->prepare($sql);
			return $this->execute($bindings, $query);
		}

		public function insert($sql, $bindings){
			$this->last_query = $sql;
			$insert = $this->connection->prepare($sql);
			$this->execute($bindings, $insert);
			return $this->connection->lastInsertId();
		}
		public function update($sql, $bindings){
			$this->last_query = $sql;
			$query = $this->connection->prepare($sql);
			return $this->execute($bindings, $query);
		}
		
	}