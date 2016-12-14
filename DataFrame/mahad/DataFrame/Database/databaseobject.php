<?php
/**
* Important Class that should always be in any project
*/
namespace DataFrame\Database;
use DataFrame\Inflect;

abstract class DatabaseObject extends DatabaseManager implements Methods, \ArrayAccess{
	protected static $table_name;
	protected static $view_name;
	protected static $pk = 'id';
	public static $db;
	public $id;
	public static $query;
	//protected $vars = array();
	public $vars = array();
	protected static $object;
	private static $selectables = array();
	private static $whereClause = array();
	private static $andWhereClause;
	private static $limit;
	private static $groupClause;
	public static $orderbyClause;
	private static $onlyOne;
    private static $count = 0;
	private static $offSet;
	private static $joinClause = array();
	/*public $created_at = false;
	public $updated_at = false;*/
	/**
	* Create a constructor to the object
	*/
	public function __construct(array $options = array()){
		self::$db = self::$connection;
		$object = get_called_class();
		if(!isset(static::$pk)){
			self::$pk = static::$pk;
		}else{
			self::$pk = "id";
		}
		if($object != "SmxView" && $object != get_class() && empty($object::$table_name)){
			$object::$table_name = Inflect::pluralize(strtolower($object));
		}
		if(isset($this->created_at) && $this->created_at == true){
			$date = new \DateTime("now");
			$this->created_at = $date->format("Y-m-d H:i:s");
		}
		if(isset($this->updated_at) && $this->updated_at == true){
			$date = new \DateTime("now");
			$this->updated_at = $date->format("Y-m-d H:i:s");
		}
		if(!empty($options)){
			$class = new $object();
			foreach($options as $field => $value){
				$class->$field = $value;
			}
			$class->save();
		}
		
		
		
	}
	/**
	* Make the object act like an array when at access time
	*
	*/
	 public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->vars[] = $value;
        } else {
            $this->vars[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->vars[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->vars[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->vars[$offset]) ? $this->vars[$offset] : null;
    }
	
	public static function getInstance() { 
        if (!is_object(self::$onlyOne)) { 
			$klass = get_called_class();
            self::$onlyOne = new $klass(); 
			self::$count++;
        } 
        return self::$onlyOne; 
    } 
	/**
	* Set a variable and make an object point to it
	*/
	public function __set($name, $value){
        $this->vars[$name] = $value;
    }
    /**
	* Set a variable and make an object point to it
	*/
    public function &__get($name) {
		$new_class = get_called_class();
		$new_instance_class = new $new_class();
		$class = new \ReflectionClass($new_class);
		//$instance = $class->newInstanceArgs();
		if($class->hasMethod('hasMany') && $class->hasMethod($name) && Inflect::pluralize($name) == $name){
			$this->vars[$name] = $this->$name()->get();
		}elseif($class->hasMethod('belongsTo') && $class->hasMethod($name)){
			$this->vars[$name] = $this->$name();
		}elseif($class->hasMethod('hasOne') && $class->hasMethod($name)){
			$this->vars[$name] = $this->$name();
		}
        return $this->vars[$name];
    }
	/**
	* Scan all the records in a given table of the get_called_class func
	*/
	public static function findAll(){
		self::$object = get_called_class();
		return new self::$object;
	}
	/**
	* alternative limit of number of returned objects
	*
	*/
	public static function take($num){
		self::limit($num);
		self::$object = get_called_class();
		return new self::$object;
	}
	/**
	* Get the first object in an array of objects
	*/
	public static function first(){
		return array_shift(self::get());
	}
	/**
	* Get the last object in an array of objects
	*/
	public static function last(){
		return array_pop(self::get());
	}
	
	/**
	* alternative find_by_id
	*
	*/
	public static function find($id){
		$result = 0;
		if(is_array($id)){
			//$result = static::where("id", "IN", $id)->get();
			$result = static::where(static::$pk, "IN", $id)->get();
		}else{
			$result = static::find_by_id($id);
		}
		return $result;
	}
	/**
	* Eager loading implementation
	* Helps with the hasMany()
	* and belongsTo
	* loads faster than both of them
	*
	*/
	public static function with($other_object){
		$caller = get_called_class();
		$caller_class = strtolower($caller);
		$ids = call_user_func($caller."::select", static::$pk)->get();
		$foreign = array();
		foreach($ids as $id){
			$foreign[] = $id->id;
		}
		self::$query  = call_user_func($other_object."::where", $caller_class."_id", "IN", $foreign);
		return new $other_object;
	}
	/**
	* return requested objects
	*
	*/
	public static function get($args = false){
		static::result();
		if($args){
			static::take($args);
			static::result();
		}
		return self::find_by_sql(self::$query);
		//return self::$query;
	}
	/**
	* return number of objects in a query
	*
	*/
	public function count(){
		self::result();
		self::$query;
		$row = self::$db->query(self::$query);
		return count($row->fetchAll(\PDO::FETCH_ASSOC));
	}
	/**
	* Group all the records in a given table of the get_called_class func
	*/
	public static function groupBy($var){
		self::$object = get_called_class();
		self::$groupClause = $var;
		return new self::$object;
	}
	/**
	* Simulates the joins of sql but always make sure the tables you
	* are joining have different names for their individual primary keys
	*/
	
	public static function join($var){
		self::$object = get_called_class();
		self::$joinClause[] = $var;
		return new self::$object;
	}
	/**
	* Order all the records in a given table of the get_called_class func
	*/
	public static function orderBy($var){
		self::$object = get_called_class();
		self::$orderbyClause = $var;
		return new self::$object;
	}
	/**
	* OffSet all the records in a given table of the get_called_class func
	*/
	public static function offSet($var){
		self::$object = get_called_class();
		self::$offSet = $var;
		return new self::$object;
	}
	/**
	* Limit all the records in a given table of the get_called_class func
	*/
	public static function limit($lower, $upper=0){
		self::$object = get_called_class();
		if($upper){
			self::$limit = $lower.", ".$upper;
		}else{
			self::$limit = $lower;
		}
		return new self::$object;
	}
	/**
	* return a few fields from an object
	*
	*/
	public static function select(){
		self::$object = get_called_class();
		self::$selectables = func_get_args();
		return new self::$object;
	}
	/**
	* destroy an object
	*
	*/
	public static function delete(){
		self::$query = "DELETE FROM ".static::$table_name;
		if(!empty(self::$whereClause))
			self::$query .= " WHERE ".implode(" AND ",self::$whereClause);
		if(!empty(self::$andWhereClause))
			self::$query .= " AND ".self::$andWhereClause;
		//return self::$query;
		$result = self::$db->query(self::$query);
		return ($result->rowCount() == 1) ? true : false;
	}
	public static function result(){
		
		if(empty(self::$selectables) || self::$selectables == "*"){
			$select = "*";
		}else{
			$select = join(",",self::$selectables);
		}
		self::$query = "SELECT ".$select." FROM ".static::$table_name;
		if(!empty(self::$joinClause)){
			foreach(self::$joinClause as $class){
				self::$query .= " JOIN ".$class::$table_name." ON ";
				self::$query .= static::$table_name.".id = ".$class::$table_name.".".strtolower(get_called_class())."_id";
			}
		}
		if(!empty(self::$whereClause))
			self::$query .= " WHERE ".implode(" AND ",self::$whereClause);
		if(!empty(self::$andWhereClause))
			self::$query .= " AND ".self::$andWhereClause;
		if(!empty(self::$groupClause))
			self::$query .= " GROUP BY ".self::$groupClause;
		if(!empty(self::$orderbyClause))
			self::$query .= " ORDER BY ".self::$orderbyClause;
		if(!empty(self::$limit))
			self::$query .= " LIMIT ".self::$limit;
		if(!empty(self::$offSet))
			self::$query .= " OFFSET ".self::$offSet;
		self::$whereClause = "";
		self::$groupClause = "";
		self::$orderbyClause = "";
		self::$limit = "";
		self::$selectables = "";
		self::$andWhereClause = "";
		self::$offSet = "";
		self::$whereClause = array();
		self::$joinClause = array();
		return self::$query;
	}
	/**
	* Implement the where in a query
	*/
	public static function where($field, $criteria=0, $value = 0){
		self::$object = get_called_class();
		if($value){
			if(is_int($value)){
				$select = "$value";
			}
			if(is_string($value)){
				$select = "'$value'";
			}
			
			if(strtolower($criteria) == "in"){
				$is_int = false;
				foreach($value as $val){
					if(is_int($val)){
						$is_int = true;
					}
				}
				if($is_int == true){
					self::$whereClause[] = "$field IN(".join(",", $value).")";
				}else{
					self::$whereClause[] = "$field IN('".join("','", $value)."')";
				}
			}elseif(strstr(strtolower($criteria), "not in")){// done later
				$is_int = false;
				foreach($value as $val){
					if(is_int($val)){
						$is_int = true;
					}
				}
				if($is_int == true){
					self::$whereClause[] = "$field NOT IN(".join(",", $value).")";
				}else{
					self::$whereClause[] = "$field NOT IN('".join("','", $value)."')";
				}
			}else{
				self::$whereClause[] = "$field $criteria $select";
			}
		}else{
			if(is_int($criteria)){
				$select = "$criteria";
			}
			if(is_string($criteria)){
				$select = "'$criteria'";
			}
			self::$whereClause[] = $field." = $select" ;
		}
		
		
		
		return new self::$object; 
	}
	public static function andWhere($field, $criteria, $value = 0){
		self::$object = get_called_class();
		if($value){
			if(is_int($value)){
				$select = "$value";
			}
			if(is_string($value)){
				$select = "'$value'";
			}
			if(strtolower($criteria) == "in"){
				$is_int = false;
				foreach($value as $val){
					if(is_int($val)){
						$is_int = true;
					}
				}
				if($is_int == true){
					self::$andWhereClause = "$field IN(".join(",", $value).")";
				}else{
					self::$andWhereClause = "$field IN('".join("','", $value)."')";
				}
			}else{
				self::$andWhereClause = "$field $criteria $select";
			}
		}else{
			if(is_int($criteria)){
				$select = "$criteria";
			}
			if(is_string($criteria)){
				$select = "'$criteria'";
			}
			self::$andWhereClause = $field." = $select" ;
		}
		return new self::$object; 
	}
	/**
	* Scan all the records in a given table of the get_called_class func and return an array of objects
	*/
	public static function find_all(){
		return static::all();
	}
	/**
	* Scan all the records in a given table of the get_called_class func and return an array of objects
	*/
	public static function all(){
		return static::select("*")->get();
	}
	public static function find_by_id($id=0){
		$class = get_called_class();
		$result_array = call_user_func($class."::where", static::$pk, $id)->first();
		return !empty($result_array)?$result_array:false;
	}
	/**
	* Build a squel from the fields provided
	*
	*/
	public static function find_by_sql($sql = ""){
		$object_array = array();
		$result = self::$db->query($sql);
		foreach($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
			$object_array[] = static::instantiate($row);
		}
		return $object_array;
	}
	/**
	* Build a find by any field in the database
	*
	*/
	public static function __callStatic($method, $args) {
		if (preg_match('/^findBy(.+)$/', $method, $matches)) {
			return static::where(strtolower($matches[1]), $args[0]);
		}
	}
	public function __call($method, $args) {
		if (preg_match('/^set(.+)$/', $method, $matches)) {
			//return static::where(strtolower($matches[1]), $args[0]);
			//$class2 = $args::getInstance();
			$newObject = get_called_class();
			$created = $newObject::getInstance();
			$foreign = strtolower($matches[1]).'_id';
			$created->$foreign = 1;
			print_r( $created);
		}
	}
	
	public static function raw(){
		return new static();
	}
	/**
	* Create a new Instance of the returned class
	* use the SingleTon pattern for this
	* returns the object created and its 
	* instantiated values
	*/
	private static function instantiate($record){
		$class_name = get_called_class();
		$object = self::injector($class_name);
		foreach($record as $attribute => $value){
			$object->$attribute = $value;
		}
		return $object;
	}
	/**
	* Inject a dependency object
	* And Inject it...
	* author->semix
	*/
	public static function injector($class){
		$reflector = new \ReflectionClass($class);

		if(! $reflector->isInstantiable()){
 			throw new \Exception("[$class] is not instantiable");
 		}
		
 		$constructor = $reflector->getConstructor();
		
 		if(is_null($constructor)){
 			return new $class;
 		}
		
 		$parameters = $constructor->getParameters();
 		$dependencies = self::getDependencies($parameters);
		
 		return $reflector->newInstanceArgs($dependencies);
	}
	
	/**
	 * Build up a list of dependencies for a given methods parameters
	 *
	 * @param array $parameters
	 * @return array
	 */
	public static function getDependencies($parameters){
		$dependencies = array();
		
		foreach($parameters as $parameter)
		{
			$dependency = $parameter->getClass();
			
			if(is_null($dependency)){
				$dependencies[] = self::resolveNonClass($parameter);
			}
			else{
				$dependencies[] = self::injector($dependency->name);
			}
		}
		
		return $dependencies;
	}
	
	/**
	 * Determine what to do with a non-class value
	 *
	 * @param ReflectionParameter $parameter
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public static function resolveNonClass(\ReflectionParameter $parameter){
		if($parameter->isDefaultValueAvailable()){
			return $parameter->getDefaultValue();
		}
		
		throw new \Exception("Erm.. Cannot inject the unkown!?");
	}
	/**
	* May not have any importance in future
	* but still used in this version
	*/
	private function has_attribute($attribute){
		$object_vars = $this->attributes();
		return array_key_exists($attribute, $object_vars);
	}
	/**
	* show fields of a given table of an object
	*
	*/
	public  function show_tables(){
		$tb_fields = array();
		if(self::$db->database_type == 'sqlite'){
			$sql = "PRAGMA table_info(".static::$table_name.")";
			$fields = self::$db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
			foreach($fields as $field){
				$tb_fields[] = $field['name'];
			}
		}else{
			$sql = "SHOW FIELDS FROM ".static::$table_name;
			$fields = self::$db->query($sql)->fetchAll(\PDO::FETCH_COLUMN);
			foreach($fields as $field=>$name){
				$tb_fields[] = $name;
			}
		}
		return $tb_fields;
	}
	protected function attributes(){
		// return an array of attribute keys and their values
		$table_fields = $this->show_tables();
		$attributes = array();
		foreach($table_fields as $field){
			//if(property_exists($this, $field)){
				$attributes[$field] = $this->{$field};
			//}
		}
		return $attributes;
	}
	/**
	* escape vals
	*
	*/
	protected function sanitized_attributes(){
		$clean_attributes = array();
		foreach($this->attributes() as $key => $value){
			$clean_attributes[$key] = self::$db->escape_value($value);
		}
		return $clean_attributes;
	}
	/**
	* decides whether to update or create an object
	*
	*/
	public function save($options = 0){
		if($options){
			$debug = debug_backtrace();
			$temp_object = $debug[0]['args'][0];
			$real_object = $debug[0]['object'];
			$saved_object = $temp_object::getInstance();
			$foreign = [];
			$foreign_key_field = 0;
			$foreign_key_value = 0;
			foreach($real_object as $field){
				$foreign[] = $field;
			}
			foreach($temp_object as $field => $value){
				$saved_object->$field = $value;
			}
			
			foreach($foreign[1] as $field => $value){
				if(strstr($field, "_id")){
					$foreign_key_field = $field;
					$foreign_key_value = $value;
				}
				
			}
			
			$saved_object->$foreign_key_field = $foreign_key_value;
			//return $saved_object;
			return isset($this->{static::$pk})? $saved_object->update() : $saved_object->create();
		}else{
			return isset($this->{static::$pk})? $this->update() : $this->create();
		}
	}
	
	public static function make(array $options){
		$object = new static();
		foreach($options as $field => $value){
			$object->$field = $value;
		}
		return $object;
		//return $object->save();
	}
	
	
	/**
	* create an object
	*
	*/
	public function create(){
		$attributes = $this->sanitized_attributes();
		if(self::$db->database_type == 'sqlite' && static::$table_name != "product"){
			array_shift($attributes);
		}
		$sql = "INSERT INTO ".static::$table_name." (";
		$sql .= join(", " ,array_keys($attributes));
		$sql .= ") VALUES('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		$result = self::$db->query($sql);
		if($result){
			if(self::$db->database_type == 'sqlite'){
				$this_insert = array_shift($result->fetchAll(\PDO::FETCH_ASSOC));
			}
			$this->{static::$pk} = self::$db->lastInsertId();
			return true;
		}else{
			return false;
		}
	}
	/**
	* update an object
	*
	*/
	public function update(){
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			$attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".static::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE ".static::$pk."=".$this->{static::$pk};
		$result = self::$db->query($sql);
		return ($result->rowCount()==1)?true : false;
	}
	/**
	* make the joins of sql queries one to many
	*
	*/
	public function hasMany($class, $through = 0){
		$caller = get_called_class();
		$constructor = call_user_func($caller."::find",$this->{static::$pk});
		$caller_id = $constructor->{static::$pk};
		if($through){
			if(is_array($through)){
				$foreign = $through['through'];
			}else{
				$foreign = $through;
			}
		}else{
			$foreign =  strtolower($caller)."_id";
			if(strstr($foreign, "\\")){
				$strings = explode("\\", $foreign);
				$last_string = $strings[count($strings)-1];
				$foreign = $last_string;
			}
			$finder = \FileLocator::instance();
			$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
			foreach($namespaces as $namespace){
				if(strstr($namespace,  $class)){
					$class = $namespace;
				}
			}
		}
		//return $class;
		call_user_func($class."::where", $foreign, $caller_id);
		$object = new $class();
		$object->$foreign = $caller_id;
		//return new $class;
		return $object;
	}
	/**
	* make the joins of sql queries many to many
	*
	*/
	public function belongsToMany($class, $through = false){
		$caller = get_called_class();
		$join_classes = array();
		$get_ids = array();
		if(strstr($caller, "\\")){
			$strings = explode("\\", $caller);
			$last_string = $strings[count($strings)-1];
			$relation_class_one = strtolower($last_string);
		}else{
			$relation_class_one = strtolower($caller);
		}
		if(strstr($class, "\\")){
			$strings = explode("\\", $class);
			$last_string = $strings[count($strings)-1];
			$relation_class_two = strtolower($last_string);
		}else{
			$relation_class_two = strtolower($class);
		}
		$join_classes[] = $relation_class_one;
		$join_classes[] = $relation_class_two;
		sort($join_classes);
		
		if($through){
			if(is_array($through)){
				if(array_key_exists("class", $through)){
					$joined_classes = $through['class'];
				}
			}else{
				$joined_classes = $through;
			}
		}else{
			$joined_classes = ucfirst($join_classes[0])."_".$join_classes[1];
		}

		$finder = \FileLocator::instance();
		$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
		foreach($namespaces as $namespace){
			if(strstr($namespace,  $joined_classes)){
				$joined_classes = $namespace;
			}
		}

		
		
		$constructor = call_user_func($caller."::find",$this->{static::$pk});
		$caller_id = $constructor->{static::$pk};
		
		$foreign =  strtolower($relation_class_one)."_id";
		
		
		$foreign_two = strtolower($relation_class_two)."_id";
		
		//return $foreign;
		$array_for_joined_classes = call_user_func($joined_classes."::where", $foreign, $this->{static::$pk})->get();
		
		foreach($array_for_joined_classes as $object){
			$get_ids[] = $object->$foreign_two;
		}
		
		//$new_array_objects = $class::where("id", "IN", $get_ids);
		$new_array_objects = $class::where($class::$pk, "IN", $get_ids);
		
		return new $class();
	}
	
	/**
	* make the joins of sql queries one to many of diffent types of objects in one type
	*
	*/
	
	public function mergeableMany($class, $search_term){
		$caller = get_called_class();
		$constructor = call_user_func($caller."::find",$this->{static::$pk});
		$caller_id = $constructor->{static::$pk};
		$foreign = $search_term."_id";
		/*if(strstr($foreign, "\\")){
			$strings = explode("\\", $foreign);
			$last_string = $strings[count($strings)-1];
			$foreign = $last_string;
		}*/
		$finder = \FileLocator::instance();
		$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
		foreach($namespaces as $namespace){
			if(strstr($namespace,  $class)){
				$class = $namespace;
			}
		}

		if(strstr($caller, "\\")){
			$strings = explode("\\", $caller);
			$last_string = $strings[count($strings)-1];
			$caller = $last_string;
		}
		//return $class;
		call_user_func($class."::where", $foreign, $caller_id)->where($search_term."_type", strtolower($caller));
		$object = new $class();
		return $object;
	}
	
	/**
	* returns one object from the caller class
	*
	*/
	
	public function mergeable(){
		$caller = get_called_class();
		
		$constructor = call_user_func($caller."::find",$this->{static::$pk});	
		$debug = debug_backtrace();
		
		$string_for_merging = $debug[1]['function'];
		$constructive_string = $string_for_merging."_type";
		
		$constructive_id = $string_for_merging."_id";
		$new_id = $constructor->$constructive_id;
		$new_class = ucfirst($constructor->$constructive_string);

		$finder = \FileLocator::instance();
		$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
		foreach($namespaces as $namespace){
			if(strstr($namespace,  $new_class)){
				$new_class = $namespace;
			}
		}
		//return $new_class;
		$new_object = call_user_func($new_class."::find", $new_id);
		return $new_object;
		
	}
	
	/**
	* make the joins of sql queries one to one
	*
	*/
	public function belongsTo($class, $foreign=0){
		$caller = get_called_class();
		$constructor = call_user_func($caller."::find",$this->{static::$pk});	
		if($foreign){
			if(is_array($foreign)){
				$caller_id = $constructor->{$foreign['through']};
			}else{
				$caller_id = $constructor->{$foreign};
			}
			$finder = \FileLocator::instance();
			$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
			foreach($namespaces as $namespace){
				if(strstr($namespace,  $class)){
					$class = $namespace;
				}
			}
		}else{
			$foreign_id = $class;
			$finder = \FileLocator::instance();
			$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
			foreach($namespaces as $namespace){
				if(strstr($namespace,  $class)){
					$class = $namespace;
				}
			}

			if(strstr($foreign_id,  "\\")){
				$strings = explode("\\", $foreign_id);
				$foreign_id = $strings[count($strings)-1];
			}
			
			$caller_id = $constructor->{strtolower($foreign_id)."_id"};
		}
		//return $foreign_id;
 		return call_user_func($class."::find", $caller_id);
	}
	
	public function hasOne($class, $foreign=0){
		$caller = get_called_class();
		$constructor = call_user_func($caller."::find",$this->{static::$pk});
			
		if($foreign){
			if(is_array($foreign)){
				$relation_id = $foreign['through'];
			}else{
				$relation_id = $foreign;
			}
		}else{
			$relation_id = strtolower($caller)."_id";
		}

		if(strstr($relation_id,  "\\")){
			$strings = explode("\\", $relation_id);
			$relation_id = $strings[count($strings)-1];
		}

		$finder = \FileLocator::instance();
		$namespaces = $finder->getClassesOfNamespace(explode("\\", $caller)[0]);
		foreach($namespaces as $namespace){
			if(strstr($namespace,  $class)){
				$class = $namespace;
			}
		}
 		return call_user_func($class."::findBy".$relation_id, $constructor->id)->first();
	}
	public function associate($object, $custom = 0){
		$caller = get_called_class();
		$object_id = strtolower(get_class($object))."_id";
		$cons = $caller::getInstance();
		if($custom){
			if(strstr($custom, "able")){
				$cons->{$custom."_type"} = strtolower(get_class($object));
			}
			$cons->{$custom."_id"} = $object->id;
		}else{
			$cons->$object_id = $object->id;
		}
		return $cons;
	}
}