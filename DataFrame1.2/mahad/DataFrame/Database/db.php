<?php
namespace DataFrame\Database;
class DB extends DatabaseManager{
	private static $table;
	private static $query;
	public static $db;
	private static $selectables = array();
	private static $whereClause = array();
	private static $limit;
	private static $groupClause;
	public static $orderbyClause;
	private static $viewQuery;
	public function __construct($extra=''){
		self::$db = parent::$connection;
	}
	public static function select(){
		self::$selectables = func_get_args();
		return new self;
	}
	public static function table($table){
		self::$table = $table;
		return new self;
	}
	public static function get(){
		self::result();
		$result = self::$db->query(self::$query);
		
		//return self::$query;
		//return $result->fetchAll(PDO::FETCH_CLASS/*, "Product"*/);
		return $result->fetchAll(\PDO::FETCH_ASSOC);
	}
	public static function count(){
		self::result();
		$result = self::$db->query(self::$query);
		return count($result->fetchAll(\PDO::FETCH_ASSOC));
	}
	public static function where($field, $param, $value=0){
		if($value){
			if((int)$value){
				$select = "$value";
			}else{
				$select = "'$value'";
			}
			self::$whereClause[] = "$field $param $select";
			
		}else{
			if((int)$param){
				$select = "$param";
			}else{
				$select = "'$param'";
			}
			self::$whereClause[] = "$field = $select";
		}
		
		return new self;
	}
	public static function where2($field, $param, $value=0){
		if($value){
			if((int)$value){
				$select = "$value";
			}else{
				$select = "'$value'";
			}
			self::$whereClause = "$field $param $select";
		}else{
			if((int)$param){
				$select = "$param";
			}else{
				$select = "'$param'";
			}
			self::$whereClause = $field." = $select" ;
		}
		return new self;
	}
	public static function groupBy($var){
		self::$groupClause = $var;
		return new self;
	}
	public static function orderBy($var){
		self::$orderbyClause = $var;
		return new self;
	}
	public static function limit($lower, $upper=0){
		if($upper){
			self::$limit = $lower.", ".$upper;
		}else{
			self::$limit = $lower;
		}
		return new self;
	}
	public static function result(){
		if(empty(self::$selectables)){
			$select = "*";
		}else{
			$select = join(",",self::$selectables);
		}
		self::$query = "SELECT ".$select." FROM ".self::$table;
		if(!empty(self::$whereClause)){
			array_unique(self::$whereClause);
			self::$query .= " WHERE ".implode(" AND ",self::$whereClause);
		}
		if(!empty(self::$groupClause))
			self::$query .= " GROUP BY ".self::$groupClause;
		if(!empty(self::$orderbyClause))
			self::$query .= " ORDER BY ".self::$orderbyClause;
		if(!empty(self::$limit))
			self::$query .= " LIMIT ".self::$limit;
			self::$groupClause = "";
			self::$orderbyClause = "";
			self::$limit = "";
			self::$selectables = "";
			self::$whereClause = array();
		
		return self::$query;
	}
	public static function first(){
		return array_shift(self::get());
	}
	public static function last(){
		return array_pop(self::get());
	}
	
	public static function statement($sql){
		self::$viewQuery = $sql;
		return new self;
	}
	public static function run(){
		$result = self::$db->query(self::$viewQuery);
	}
	
	
}