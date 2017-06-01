<?php
namespace DataFrame\Database\Elegant\Association;
use DataFrame\Database\Elegant\Model;
use DataFrame\Database\Query\Builder;
use DataFrame\Database\Elegant\Collection;

class BelongsToMany extends Association{
    protected $table;
    protected $foreignKey;

    protected $otherKey;
    protected $pivotColumns = [];
    protected $pivotWheres = [];

    protected $pivotWhereIns = [];

    protected $pivotCreatedAt;
    
    public function __construct(Builder $query, Model $parent, $table, $foreignKey, $otherKey){
        $this->table = $table;
        $this->otherKey = $otherKey;
        $this->foreignKey = $foreignKey;
        $this->parent = $parent;
        $this->query = $query;

        
        parent::__construct($query, $parent);
    }

    public function addConditions(){
        $this->setJoins();
        $this->setWhereClause();
    }
     protected function setWhereClause(){
        $foreign = $this->getForeignKey();
        $this->query->where($foreign, '=', $this->getParentIdValue());//->select($this->query->from.'.*');
        return $this;
    }

    public function getParentIdValue(){
        return $this->parent->getAttribute($this->parent->getPrimaryKey());
    }

    public function getForeignKey(){
        return $this->table.'.'.$this->foreignKey;
    }

    protected function setJoins($query = null){
        $query = $query ? $query:$this->query;
        $baseTable = $this->query->getModel()->getTable();
        
        $key = $baseTable.'.'.$this->query->getModel()->getPrimaryKey();
        
        $query->join($this->table, $key, '=', $this->getOtherKey());
        
        return $this;
    }
    protected function getOtherKey(){
        return $this->table.'.'.$this->otherKey;
    }
}