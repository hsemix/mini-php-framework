<?php
/**
* Important Class that should always be in any project
*/
namespace DataFrame\Database\Elegant;
use Iterator;
use ArrayAccess;
use DataFrame\Inflect;
use DataFrame\Arr;

class Collection  implements ArrayAccess, Iterator{
    protected $items = [];
    protected static $instances = [];
    public function __construct($items = []){
        $this->items = $items;
    }
    public function addItem($item){
        $this->items = $item;
    }

    public function getItems(){
        return $this->items;
    }

    public function first(){
        return $this->items[0];
    }

    public function last(){
        return $this->items[count($this->items) - 1];
    }


    
    /**
     * Find a model in the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return \DataFrame\Database\Elegant\Model
     */
    public function find($key, $default = null){
        if ($key instanceof Model) {
            $key = $key->getPrimaryKey();
        }

        return Arr::first($this->items, function ($itemKey, $model) use ($key) {
            return $model->getPrimaryKey() == $key;
        }, $default);
    }

    final public static function instance(){
		$class_name = get_called_class();

		if (!isset(self::$instances[$class_name]))
			self::$instances[$class_name] = new $class_name;

		return self::$instances[$class_name];
	}

    /**
	* Make the object act like an array when at access time
	*
	*/
	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
    

   

    public function rewind(){
        reset($this->items);
    }
  
    public function current(){
        $var = current($this->items);
        return $var;
    }
  
    public function key() {
        $var = key($this->items);
        return $var;
    }
  
    public function next() {
        $var = next($this->items);
        return $var;
    }
  
    public function valid(){
        $key = key($this->items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(){
        return count($this->items);
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  callable|null  $callback
     * @return static
     */
    public function filter(callable $callback = null){
        if ($callback) {
            $return = [];

            foreach ($this->items as $key => $value) {
                if ($callback($value, $key)) {
                    $return[$key] = $value;
                }
            }

            return new static($return);
        }

        return new static(array_filter($this->items));
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $strict
     * @return static
     */
    public function where($key, $value, $strict = true){
        return $this->filter(function ($item) use ($key, $value, $strict) {
            return $strict ? data_get($item, $key) === $value
                           : data_get($item, $key) == $value;
        });
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all(){
        return $this->items;
    }

    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null){
        if ($this->offsetExists($key)) {
            return $this->items[$key];
        }

        return value($default);
    }

}