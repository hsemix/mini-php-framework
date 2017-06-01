<?php
namespace DataFrame\Database\Query;
use PDO;
use Closure;
use DataFrame\Database\Elegant\Model;
use DataFrame\Database\Elegant\Collection;
class Builder{
    public $connection;
    public $from;
    public $query;
	public $model;
	public $selectables = [];
	public $whereClause;
	public $limit;
	public $groupClause;
	public $orderbyClause;
	public $offSet;
	public $isClosure = false;
	public $lastInsertId = null;
	public $bindings;
	public $joinClause = [];
	public $delete = false;
	public $relations = [];
    public function __construct($connection, Model $model){
        $this->table($model->getTable());
		$this->connection = $connection;
		$this->model = $model;
    }
    public function all($columns = ['*']){
		$models = $this->getAll($columns);
        return new Collection($models);
	}

	public function getModels($columns = ['*']){
        
        $results = $this->getAll($columns);   
        return $this->model->makeModels($results);
    }

	/**
	* get the model that is calling this query
	*/
	

	public function getModel(){
		return $this->model;
	}
    /**
	* return a few fields from an object
	*
	*/
	public function select(){
		//if(empty($this->selectables)){
			$this->selectables = func_get_args();
		//}
		
		return $this;
	}

    public function table($from){
        $this->from = $from;
    }

	/**
	* Group all the records in a given table of the get_called_class func
	*/
	public function groupBy($var){
		$this->groupClause = $var;
		return $this;
	}

	/**
	* OffSet all the records in a given table of the get_called_class func
	*/
	public function offSet($var){
		$this->offSet = $var;
		return $this;
	}

    /**
	* return requested objects
	*
	*/


	public function getAll($columns = ['*']){
        if($columns){
            $this->query = $this->select(implode(",", $columns));
        }
		$sql = $this->findBySql($this->result());
		return $sql;
    }
	
	public function get($columns = ['*']){
		$models = $this->getAll($columns);
		
		/*if(!empty($this->relations)){
			foreach($models as $model){
				//$models = [];
				$model->relations = $this->relations->where()->get();
				$models[] = $models;
			}
		}*/

        return $this->getModel()->newCollection($models);
        //return new Collection($models);
	}

	public function result(){
		if(empty($this->selectables) || $this->selectables == "*"){
			$select = $this->addBackTicks($this->from).".*";
		}else{
			
			$selectables = [];
			foreach($this->selectables as $selectable){
				$selectables[] = $this->addBackTicks($this->from).'.'.$selectable;
			}
			$select = implode(", ", $selectables);
		}
		$this->query = "SELECT ".$select." FROM ".$this->addBackTicks($this->from);
		if(!empty($this->joinClause)){
			foreach($this->joinClause as $class){
				$joinType = strtoupper($class->type);
				$this->query .= " {$joinType} JOIN ".$this->addBackTicks($class->table)." ON ";
				$this->query .= $class->clauses[0]['first']." ".$class->clauses[0]['operator']." ".$class->clauses[0]['second'];
			}
		}
		if(!empty($this->whereClause))
			$this->query .= $this->whereClause;
		if(!empty($this->groupClause))
			$this->query .= " GROUP BY ".$this->groupClause;
		if(!empty($this->orderbyClause))
			$this->query .= " ORDER BY ".$this->orderbyClause;
		if(!empty($this->limit))
			$this->query .= " LIMIT ".$this->limit;
		if(!empty($this->offSet))
			$this->query .= " OFFSET ".$this->offSet;
		$this->whereClause = "";
		$this->groupClause = "";
		$this->orderbyClause = "";
		$this->limit = "";
		$this->selectables = "";
		$this->offSet = "";
		$this->whereClause = [];
		$this->joinClause = [];
		$this->delete = false;
		return $this->query;
	}

    /**
	* Build a squel from the fields provided
	*
	*/
	public function findBySql($sql = ""){
		$result = $this->connection->pdoQuery($sql, $this->getBindings());
		$collection = [];
        $query_results = $result->fetchAll(PDO::FETCH_ASSOC);
		foreach($query_results as $row){
			$collection[] = $this->model->newFromQuery($row);
		}
		return $collection;
	}
 
	/**
	* Build a squel from the fields provided
	*
	*/
	public function findByRaw($sql = ""){
		$result = $this->connection->query($sql);
		$object_array = $result->fetchAll(PDO::FETCH_ASSOC);
		return $object_array;
	}

	

	public function runClosure(Closure $function, $type){
		$this->isClosure = true;
		
		$this->whereClause .= (!$this->whereClause) ? ' WHERE' : ' ' . strtoupper($type) . ' ';
		$this->whereClause .= ' (';
		call_user_func_array($function , array($this));
		$this->whereClause .= ') ';
		$this->isClosure = false;
	}

	protected function processWhereIn($column, $values){
		$bindings = [];
		foreach($values as $value){
			$bindings[] = '?';
		}
		$this->bindings[] = $values;
		$sql = "`{$this->from}`.`{$column}` IN (".implode(", ", $bindings).")";
		return $sql;
	}

	protected function processWhereNotIn($column, $values){
		$bindings = [];
		foreach($values as $value){
			$bindings[] = '?';
		}
		$this->bindings[] = $values;
		$sql = "`{$this->from}`.`{$column}` NOT IN (".implode(", ", $bindings).")";
		return $sql;
	}

	protected function buildWhereClause($type , $column , $operator = null , $value = null){
		$this->whereClause .= ($this->whereClause)?' '.strtoupper($type).' ':' WHERE ';
		
		if(strtolower($operator) == "in"){
			$this->whereClause .= $this->processWhereIn($column, $value);				
		}elseif(strstr(strtolower($operator), "not in")){
			$this->whereClause .= $this->processWhereNotIn($column, $value);
		}else{
			$this->whereClause .= $column." ". $operator." ?";//. $select;
			$this->bindings[] = $value;
		}
		return $this;
	}


    /**
	* Implement the where in a query
	*/
	public function where($column, $operator = null, $value = null, $type = 'and'){
		if($column instanceof Closure){
			$this->runClosure($column, $type);
			return $this;
		}
		if($this->isClosure){
			$this->isClosure = false;
			$type = '';
		}
		if(func_num_args() == 2){
			$valueTaken = $operator;
			$operatorSymbol = "=";
			if($valueTaken instanceof Closure){
				$this->whereSub($column, $type);
				return $this;
			}
		}else{
			$valueTaken = $value;
			$operatorSymbol = $operator;
		}
		
		$this->buildWhereClause($type , $column , $operatorSymbol , $valueTaken); 
		return $this;
	}
	public function orWhere($column, $operator = null, $value = null){
		if(func_num_args() == 2){
			$valueTaken = $operator;
			$operatorSymbol = "=";
		}else{
			$valueTaken = $value;
			$operatorSymbol = "=";
		}
		return $this->where($column, $operatorSymbol, $valueTaken, 'or');
	}

     /**
     * Execute a query for a single record by ID.
     *
     * @param  int    $id
     * @param  array  $columns
     * @return mixed|static
     */
    public function find($id, $columns = ['*']){
		
		if(is_array($id)){
			return $this->whereIn($this->model->getPrimaryKey(), $id)->get($columns);
		}
        return $this->where($this->model->getPrimaryKey(), '=', $id)->first($columns);
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int  $value
     * @return \DataFrame\Database\Query\Builder|static
     */
    public function take($value){
        return $this->limit($value);
    }

    /**
	* Limit all the records in a given table of the get_called_class func
	*/
	public function limit($lower, $upper=0){
		if($upper){
			$this->limit = $lower.", ".$upper;
		}else{
			$this->limit = $lower;
		}
		return $this;
	}

	public function whereIn($column, $values){
		$this->where($column, "IN", $values);
		return $this;
	}

	public function whereNotIn($column, $values){
		$this->where($column, "NOT IN", $values);
		return $this;
	}
	/**
	* Order all the records in a given table of the get_called_class func
	*/
	public function orderBy($var){
		$this->orderbyClause = $var;
		return $this;
	}
	/**
	* Get the first object in an array of objects
	*/
	public function first($columns = ['*']){
		$collection = $this->getAll($columns);
		return array_shift($collection);
	}
	/**
	* Get the last object in an array of objects
	*/
	public function last($columns = ['*']){
		$collection = $this->getAll($columns);
		return array_pop($collection);
	}

	public function toSql(){
		if($this->delete){
			return $this->query;
		}
		return $this->result();
	}


	public function count(){
		$row = $this->connection->pdoQuery($this->result(), $this->getBindings());
		//$row = $this->connection->query($this->result());
		$results = $row->fetchAll(PDO::FETCH_ASSOC);
		return count($results);
	}
	public function addBackTicks($value){
		return "`".$value."`";
	}

	public function getBindings(){
		if(!is_null($this->bindings)){
			return self::normalize($this->bindings);
		}
	}
	public static function normalize($array, $depth = INF){
		$result = [];
		
		foreach ($array as $item) {
			$item = $item instanceof Collection ? $item->all() : $item;

			if (is_array($item)) {
				if ($depth === 1) {
					$result = array_merge($result, $item);
					continue;
				}

				$result = array_merge($result, static::normalize($item, $depth - 1));
				continue;
			}

			$result[] = $item;
		}

		return $result;
	}

	public function find_by_sql($sql = null){
        $newCollection = [];
		
        $results = $this->findByRaw($sql);
        foreach($results as $row){
            $model = new $this->model;
            $model->setRawAttributes((array) $row, true);
            $newCollection[] = $model;
        }
        return $newCollection;
    }

	public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false){
        if ($one instanceof Closure) {
            $join = new JoinClause($type, $table);

            call_user_func($one, $join);

            $this->joinClause[] = $join;

        }else {
            $join = new JoinClause($type, $table);

            $this->joinClause[] = $join->on(
                $one, $operator, $two, 'and', $where
            );
        }

        return $this;
	}
	public function insert(array $values){
		$fields = null;
		$fieldValues = null;
		
        if (empty($values)) {
            return true;
        }
        if(!is_array(reset($values))) {
            $values = [$values];
			
        }else{
			
            foreach($values as $key => $value){
                //ksort($value);
                $values[$key] = $value;
            }
        }
		
		
        $bindings = [];

        foreach($values as $record){
			if(count($record) > 1){
				$fields[0] = array_keys($record);		
				$bindings[] = array_values($record);
				$i = 0;
				foreach($fields[$i] as $ff){
					$fieldValues[$i] = "?";
					$fields[$i] = $this->addBackTicks($ff);
					$i++;
				}
			}else{
				$i = 0;
				foreach($record as $key => $value){
					$bindings[] = $value;
					$fields[$i] = $this->addBackTicks($key);
					$fieldValues[$i] = "?";
					$i++;
				}
			}
        }
		$sql = "INSERT INTO ".$this->from." (".implode(", ", $fields).")"." VALUES(".implode(", ", $fieldValues).")";
		
        $this->connection->insert($sql, $bindings);

		$this->lastInsertId = $this->connection->lastInsertId();
		return $this;
    }

	public function getLastInsertId(){
        return $this->connection->lastInsertId();
    }

	public function update(array $values){
        $bindings = array_values(array_merge($values, $this->getBindings()));
		$attribute_pairs = [];
		foreach(array_keys($values) as $key){
			$attribute_pairs[] = "{$this->addBackTicks($key)} = ?";
		}
		$sql = "UPDATE {$this->addBackTicks($this->from)} SET ".implode(",", $attribute_pairs);
		$sql .= " WHERE {$this->addBackTicks($this->model->getPrimaryKey())} = ?";
		$this->connection->update($sql, $bindings);
		return $this->model;
	}

	public function firstOrCreate(array $attributes){
		foreach($attributes as $attribute => $value){
			$this->where($attribute, $value);
		}
        if (!is_null($instance = $this->first())) {
            return $instance;
        }

        $instance = $this->model->newInstance($attributes);

        $instance->save();

		return $instance;
    }

	public function updateOrCreate(array $attributes, array $values = []){
        $instance = $this->firstOrNew($attributes);

        $instance->fillModelWith($values)->save();

        return $instance;
    }

	public function firstOrNew(array $attributes){
        foreach($attributes as $attribute => $value){
			$this->where($attribute, $value);
		}
        if (!is_null($instance = $this->first())) {
            return $instance;
        }

        return $this->model->newInstance($attributes);
    }

	public function delete($permanent = false){
		$this->delete = true;
		if($permanent){
			return $this->forceDelete();
		}
		echo  'n';
	}

	private function forceDelete(){
		$this->query = "DELETE FROM `{$this->from}` {$this->whereClause}";
		return $this;
	}

	/**
     * Set the relationships that should be eager loaded.
     *
     * @param  mixed  $relations
     * @return $this
     */
    public function with($relations){
        
        if (is_string($relations)) {
            $relations = func_get_args();
        }
        
        $relations = $this->createWithRelations($relations);
        
        $this->relations = array_merge($this->relations, $relations);

        return $this;
    }

    protected function createWithRelations(array $relations){
    	$results = [];
       // print_r($relations);
        //die();
        foreach ($relations as $name => $constraints) {
            if (is_numeric($name)) {
                $f = function () {
                    //
                };

                list($name, $constraints) = [$constraints, $f];
            }
            
            $results = $this->parseNestedWith($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param  string  $name
     * @param  array   $results
     * @return array
     */
    protected function parseNestedWith($name, $results){
        $progress = [];
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;

            if (!isset($results[$last = implode('.', $progress)])) {
                $results[$last] = function () {
                    //
                };
            }
        }
        $this->model->relations = $results;
        return $results;
    }
}