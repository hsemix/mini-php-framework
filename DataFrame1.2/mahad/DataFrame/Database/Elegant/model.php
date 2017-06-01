<?php
/**
* Important Class that should always be in any project
*/
namespace DataFrame\Database\Elegant;

use Exception;
use ArrayAccess;
use ReflectionClass;
use DataFrame\Inflect;
use DataFrame\Database\DB;
use DataFrame\Database\Support\Helpers\FileLocator;
use DataFrame\Database\Query\Builder as QueryBuilder;
use DataFrame\Database\ElegantManager\DatabaseManager;
use DataFrame\Database\Elegant\Association\HasOne;
use DataFrame\Database\Elegant\Association\HasMany;
use DataFrame\Database\Elegant\Association\BelongsTo;
use DataFrame\Database\Elegant\Association\Mergeable;
use DataFrame\Database\Elegant\Association\BelongsToMany;
use DataFrame\Database\Elegant\Association\MergeableMany;

abstract class Model extends DatabaseManager implements ArrayAccess{
	protected static $table_name;
	protected static $view_name;
	protected static $primaryKey = 'id';
	public static $db;
	public $exists;
	private static $calledClass;
	private static $instances = [];
	private $attributes = [];
	private $original = [];
	protected static $object;
	
	private static $onlyOne;
    private static $count = 0;
	private static $savedOjbect;
	const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    public $relations;
	/**
	* Create a constructor to the object
	*/
	public function __construct(array $options = array()){
		self::$db = self::$connection;	
		$this->syncAttributes();

		$this->fillModelWith($options);		
	}
	final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}

	public function fillModelWith(array $attributes){
         foreach($attributes as $key => $value){
             $this->setAttribute($key, $value);
         }        
        return $this;
    }
	 /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncAttributes(){
        $this->original = $this->attributes;

        return $this;
    }
	/**
	* Make the object act like an array when at access time
	*
	*/
	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
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
	* get a variable and make an object point to it
	*/
    

    public function __get($key){
        return $this->getAttribute($key);
    }
	/**
	* Set a variable and make an object point to it
	*/
    public function __set($key, $value){
        $this->setAttribute($key, $value);
    }
	
	
	
	
	/**
	* Eager loading implementation
	* Helps with the hasMany()
	* and belongsTo
	* loads faster than both of them, also solves n+1 loop problems
	*
	*/
	public function with($args){
	
        if(is_string($args)) {
            $relations = func_get_args();
        }

        $instance = new static;

        return $instance->newElegantQuery()->with($relations);
	}
	

	

	
	
	

	/**
	* Build a find by any field in the database
	*
	*/

	public function __call($method, $parameters){
        $query = $this->newElegantQuery(); 
        if($method != 'findBySql' || $method !='findByRaw'){
           if (preg_match('/^findBy(.+)$/', $method, $matches)) {
                return $this->where(strtolower($matches[1]), $parameters[0]);
            }
        }
        return call_user_func_array([$query, $method], $parameters); 
        
    }
	public static function __callStatic($method, $args) {
        $instance = new static;
        if($method !== 'findBySql' && $method !=='findByRaw'){
    		if (preg_match('/^findBy(.+)$/', $method, $matches)) {
    			return static::where(strtolower($matches[1]), $args[0]);
    		}
        }

		return call_user_func_array([$instance, $method], $args);
	}

	public function newInstance($attributes = [], $exists = false){
        $model = new static((array) $attributes);

        $model->exists = $exists;

        return $model;
    }

	public function newElegantQuery(){
		return $this->newElegantMainQueryBuilder($this);
	}


	protected function newElegantMainQueryBuilder($model = null){
        return new QueryBuilder($this->getConnection(), $model);
    }

	private function getConnection(){
		return self::$db;
	}

	public function getTable(){
		if(isset(static::$table_name)) {
            return static::$table_name;
        }
        $calledClass =  class_base(static::class);
            
        return Inflect::pluralize(strtolower($calledClass));
	}

	public static function getFromView($constraints = null){
        $objectCalling = get_called_class();
        if($constraints){
            $objectCallingView = $constraints;
        }else{
            if(isset(static::$view_name)){
                $objectCallingView = static::$view_name;
            }else{
                $objectCallingView = strtolower(class_base($objectCalling))."_view";
            }
        }
        static::$table_name = $objectCallingView;
        $objectNewTable = static::$table_name;
        return new static;
    }

	 public function getPrimaryKey(){
        if(isset(static::$primaryKey)){
            return static::$primaryKey;
        }
        return self::$primaryKey;
    }

    public function newCollection(array $models = []){
        return new Collection($models);
    }

    public static function makeModels($items){ // make models from plain arrays got from db
        
        $instance = new static;
        $items = array_map(function ($item) use ($instance) {
            return $instance->newFromQuery($item);
        }, $items);

        return $instance->newCollection($items);
    }

	public function newFromQuery($attributes = []){
        $model = $this->newInstance([], true);
        $model->setRawAttributes((array) $attributes, true);
        $model->attributes = $attributes;
        
        return $model;
    }

    public function setRawAttributes(array $attributes, $sync = false){
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncAttributes();
        }

        return $this;
    }

	public function toJson($options = 0){
        return json_encode($this->jsonSerialize(), $options);
    }

    public function __toString(){
        return $this->toJson();
    }

    private function jsonSerialize(){
        return (array)$this->attributes;
    }

    public function toArray(){
        return $this->jsonSerialize();
    }

	
    public function setAttribute($key, $value){
        $this->attributes[$key] = $value;
        
        return $this;
    }




    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($name){
		$new_instance_class = $this->newInstance([], true);
		$class = new ReflectionClass($new_instance_class);
		if($class->hasMethod('hasMany') && $class->hasMethod($name) && Inflect::pluralize($name) == $name){
			$this->attributes[$name] = $this->$name()->get();
		}elseif($class->hasMethod('belongsTo') && $class->hasMethod($name)){
			$this->attributes[$name] = $this->$name()->first();
		}elseif($class->hasMethod('hasOne') && $class->hasMethod($name)){
			$this->attributes[$name] = $this->$name()->first();
		}
        return $this->attributes[$name];
    }
	/**
	* decides whether to update or create an object
	*
	*/

	public function save(array $options = []){
        $query = $this->newElegantQuery();
        if($this->exists){
            $saved = $this->performUpdate($query, $options);
        }else{
            $saved = $this->performInsert($query, $options);
        }
        return $saved;
    }

	/**
	* create and save an object
	*
	*/
	protected function performInsert(QueryBuilder $query, array $options = []){    
        $attributes = $this->attributes;
        $query->insert($attributes);
        $this->exists = true;
        $this->{$this->getPrimaryKey()} = $query->getLastInsertId();
        return true;
    }

    public function getUpdatedAtColumn(){
        return static::UPDATED_AT;
    }
	/**
	* update an object
	*
	*/
    public function performUpdate(QueryBuilder $query, array $options = []){
        if(!$this->getDirty()){
            return;
        }
        $this->setKeysForSaveQuery($query)->update($this->getDirty());
        return true;
    }

    /**
    * Remove an Object either softly or permanently
    */

    public function delete($permanent = false){
        if (is_null($this->getPrimaryKey())){
            throw new Exception('No primary key defined on model.');
        }

        if ($this->exists){
            $this->exists = false;
            return $this->performDeleteOnModel($permanent);
            
            //return true;
        }
    }

    public function performDeleteOnModel($permanent = false){
        return $this->setKeysForSaveQuery($this->newElegantQuery())->delete($permanent);
    }

    protected function setKeysForSaveQuery(QueryBuilder $query){
        $query->where($this->getPrimaryKey(), '=', $this->getKeyForSaveQuery());

        return $query;
    }

    protected function getKeyForSaveQuery(){
        return $this->getAttribute($this->getPrimaryKey());
    }

    public static function create(array $attributes = []){
        //self::$massAssign = false;
        $self = new static();
        $model = $self->make($attributes);
		$model->save();
        return $model;
    }
    public function isDirty($attributes = null){
        $dirty = $this->getDirty();
		
        if (is_null($attributes)) {
            return count($dirty) > 0;
        }

        if (! is_array($attributes)) {
            $attributes = func_get_args();
        }

        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $dirty)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty(){
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key] &&
                                 ! $this->originalIsNumericallyEquivalent($key)) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Determine if the new and old values for a given key are numerically equivalent.
     *
     * @param  string  $key
     * @return bool
     */
    protected function originalIsNumericallyEquivalent($key){
        $current = $this->attributes[$key];

        $original = $this->original[$key];

        return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
    }
	
	public static function make(array $options){
		$object = new static($options);
		return $object;
	}
	/**
     * return the appropriate namespace of the model
     *
     * @param  string  $related
     * @return \Namespace\$related
     */
    protected function returnAppropriateNamespace($related){
        $finder = FileLocator::instance();
        $classes = explode("\\", static::class);
        if(count($classes) > 1){
            $namespaces = $finder->getClassesOfNamespace(explode("\\", static::class)[0]);
            foreach($namespaces as $namespace){
                if(strstr($namespace,  $related)){
                    $className = explode("\\", $namespace);
                    if($className[count($className)-1] == $related){
                        $related = $namespace;
                    }
                }
            }
        }
       
        return $related;
    }

	/**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey(){

        return strtolower(class_base($this).'_id');
    }

	/**
	* make the joins of sql queries one to many
	*
	*/
	public function hasMany($class, $foreignKey = null, $otherKey = null){
		$foreignKey = $foreignKey?$foreignKey:$this->getForeignKey();
		$otherKey = $otherKey?$otherKey:$this->getPrimaryKey();
		$class = $this->returnAppropriateNamespace($class);
		$instance = new $class;
		return new HasMany($instance->newElegantQuery(), $this, $instance->getTable().'.'.$foreignKey, $otherKey);
	}
	/**
	* make the joins of sql queries many to many
	*
	*/

	public function belongsToMany($class, $table_name = null, $first_table_primary_key = null, $second_table_primary_key=null){
		$first_table_primary_key = $first_table_primary_key ?: $this->getForeignKey();
        $class = $this->returnAppropriateNamespace($class);
        $instance = new $class;

        $second_table_primary_key = ($second_table_primary_key)?$second_table_primary_key : $instance->getForeignKey();
		
        if (is_null($table_name)) {
            $table_name = $this->joinTables($class);
        }
		
        $query = $instance->newElegantQuery();
		
        return new BelongsToMany($query, $this, $table_name, $first_table_primary_key, $second_table_primary_key);
	}

	public function joinTables($class) {
        $base = strtolower(class_base($this));

        $class = strtolower(class_base($class));

        $models = [$class, $base];
        sort($models);
        return strtolower(implode('_', $models));
    }

    public static function firstElementInArray($array, callable $callback = null, $default = null){
        if (is_null($callback)) {
            return empty($array) ? $default : reset($array);
        }
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return $default;
    }
	
	
	/**
	* make the joins of sql queries one to many of diffent types of objects in one type
	*
	*/

	public function mergeableMany($class, $mergeable_name, $mergeable_type = null, $mergeable_id = null, $primaryKey = null){
		$class = $this->returnAppropriateNamespace($class);

		$instance = new $class;

        list($mergeable_type, $mergeable_id) = $this->getMergeStrings($mergeable_name, $mergeable_type, $mergeable_id);
		$table = $instance->getTable();

        $primaryKey = $primaryKey?$primaryKey:$this->getPrimaryKey();

        return new MergeableMany($instance->newElegantQuery(), $this, $table.'.'.$mergeable_type, $table.'.'.$mergeable_id, $primaryKey);
		
	}

	public function getMergeableClass(){
		return static::class;
	}	
	/**
	* returns one object from the caller class
	*
	*/
	public function mergeable($mergeable_name = null, $mergeable_type = null, $mergeable_id = null){
		$instance = new static;
		$debug = debug_backtrace();
		
		$string_for_merging = $debug[1]['function'];
        if(!$mergeable_name){
            $mergeable_name = $string_for_merging;
        }

		list($mergeable_type, $mergeable_id) = $this->getMergeStrings($mergeable_name, $mergeable_id, $mergeable_id);
		$class = ucfirst($this->$mergeable_type);
		$class = $this->returnAppropriateNamespace($class);
		$instance = new $class;	

        return new Mergeable($instance->newElegantQuery(), $this, $mergeable_id, $instance->getPrimaryKey(), $mergeable_type, $instance);
	}
	protected function getMergeStrings($name, $type = null, $id = null){
		if(!$type){
			$type = $name."_type";
		}
		if(!$id){
			$id = $name."_id";
		}

		return [$type, $id];
	}
	
	/**
	* make the joins of sql queries one to one
	*
	*/

	 public function belongsTo($class, $foreignKey = null, $otherKey = null, $relation = null){

        if (is_null($relation)) {
            list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $relation = $caller['function'];
        }

        if (is_null($foreignKey)) {
            $foreignKey = $relation.'_id';
        }
        $class = $this->returnAppropriateNamespace($class);
        $class = new $class;

        $query = $class->newElegantQuery();
        $otherKey = $otherKey?$otherKey:$class->getPrimaryKey();

        return new BelongsTo($query, $this, $foreignKey, $otherKey, $class);
    }

	public function hasOne($class, $foreignKey = null, $otherKey = null){
		$foreignKey = $foreignKey?$foreignKey:$this->getForeignKey();
		$otherKey = $otherKey?$otherKey:$this->getPrimaryKey();
		$class = $this->returnAppropriateNamespace($class);
		$instance = new $class;
		return new HasOne($instance->newElegantQuery(), $this, $instance->getTable().'.'.$foreignKey, $otherKey);
	}

}