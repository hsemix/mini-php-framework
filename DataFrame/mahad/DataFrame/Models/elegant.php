<?php
namespace DataFrame\Models;
use DataFrame\Database\DatabaseObject;
use DataFrame\Universal;
	class Elegant extends DatabaseObject{
		use Universal;
		public $login_id;
		public $req;
		public $res;
		public function __construct(array $options = array()){
			$this->login_id = $this->getLoggedInUserId();
			$this->req = $this->getRequest();
			$this->res = $this->getResponse();
			parent::__construct($options);
		}
		/**
		* make selection from sql views possible
		*/
		public static function getFromView($contrants = 0){
			$objectCalling = get_called_class();
			if($contrants){
				$objectCallingView = $contrants;
			}else{
				if(isset($objectCalling::$view_name)){
					$objectCallingView = $objectCalling::$view_name;
				}else{
					$objectCallingView = strtolower($objectCalling)."_view";
				}
			}
			$objectCalling::$table_name = $objectCallingView;
			$objectNewTable = $objectCalling::$table_name;
			return new $objectCalling;
		}
		
		public static function getFromTab($contrants = 0){
			$objectCalling = get_called_class();
			$objectCalling::$table_name = static::$table_name;
			$objectNewTable = $objectCalling::$table_name;
			return new $objectCalling;
		}
		public static function toJson(){
			$objectCalling = get_called_class();
			$require = $objectCalling::get();
			$ooz = [];
			foreach($require as $field){
				$ooz[] = $field->vars;
			}
			return json_encode($ooz, JSON_FORCE_OBJECT);
		}
		
		public static function toArray(){
			$objectCalling = get_called_class();
			$require = $objectCalling::get();
			$ooz = [];
			foreach($require as $field){
				$ooz[] = $field->vars;
			}
			return $ooz;
		}
	}